<?php

class RemboursementService
{
    /**
     * Calcule l'annuité constante pour un prêt incluant l'assurance mensuelle
     * Formule: A = C * [i * (1+i)^n] / [(1+i)^n - 1] + assurance_mensuelle
     * où A = annuité totale, C = capital, i = taux d'intérêt mensuel, n = nombre de mensualités
     */
    public static function calculerAnnuite($capital, $tauxAnnuel, $dureeMois, $assurance_pourcentage = 0)
    {
        $tauxMensuel = $tauxAnnuel / 100 / 12; // Conversion du taux annuel en taux mensuel

        // Calcul de l'annuité de base (capital + intérêts)
        if ($tauxMensuel == 0) {
            $annuite_base = $capital / $dureeMois; // Si pas d'intérêts, remboursement linéaire
        } else {
            $facteur = pow(1 + $tauxMensuel, $dureeMois);
            $annuite_base = ($capital * ($tauxMensuel * $facteur) / ($facteur - 1));
        }

        // Calcul de l'assurance mensuelle (% du capital initial)
        $assurance_mensuelle = $capital * ($assurance_pourcentage / 100 / 12);

        // Retourner l'annuité totale (annuité + assurance mensuelle)
        return round($annuite_base + $assurance_mensuelle, 2);
    }

    /**
     * Génère le tableau d'amortissement complet pour un prêt
     */
    public static function genererTableauAmortissement($pretId, $capital, $tauxAnnuel, $dureeMois, $dateDebut, $delai = 0)
    {
        $db = getDB();
        $tauxMensuel = $tauxAnnuel / 100 / 12;
        $annuite = self::calculerAnnuite($capital, $tauxAnnuel, $dureeMois);

        $capitalRestant = $capital;
        $dateEcheance = date('Y-m-d H:i:s', strtotime("+$delai months", strtotime($dateDebut)));
        $remboursements = [];

        for ($i = 1; $i <= $dureeMois; $i++) {
//            echo $i;
            $interets = $capitalRestant * $tauxMensuel;
            $capitalRembourse = $annuite - $interets;

            if ($i == $dureeMois) {
                $capitalRembourse = $capitalRestant;
                $annuite = $capitalRembourse + $interets;
            }

            // Ajouter les données dans le tableau
            $remboursements[] = [
                $pretId,
                $i,
                round($annuite, 2),
                $dateEcheance,
                'en_attente'
            ];

            $capitalRestant -= $capitalRembourse;
            $dateEcheance = date('Y-m-d H:i:s', strtotime("+1 months", strtotime($dateEcheance)));
        }

        // Préparer une requête pour insérer tous les remboursements
        $placeholders = implode(',', array_fill(0, count($remboursements), '(?, ?, ?, ?, ?)'));
        $values = array_merge(...$remboursements);
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("DELETE FROM s4_bank_remboursement WHERE pret_id = ?");
            $stmt->execute([$pretId]);

            $stmt = $db->prepare(
                "INSERT INTO s4_bank_remboursement
                (pret_id, numero_echeance, montant_prevu, date_echeance, statut)
                VALUES $placeholders"
            );
            $stmt->execute($values);
            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /**
     * Récupère tous les remboursements d'un prêt
     */
    public static function getRemboursementsByPret($pretId)
    {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT r.*, p.montant_accorde, tp.taux_interet,
                   e.nom as etudiant_nom, e.prenom as etudiant_prenom
            FROM s4_bank_remboursement r
            JOIN s4_bank_pret p ON r.pret_id = p.id
            JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
            JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
            WHERE r.pret_id = ?
            ORDER BY r.numero_echeance
        ");
        $stmt->execute([$pretId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les remboursements avec détails
     */
    public static function getAllRemboursements()
    {
        $db = getDB();
        $stmt = $db->query("
            SELECT r.*, p.montant_accorde, tp.taux_interet,
                   e.nom as etudiant_nom, e.prenom as etudiant_prenom,
                   ef.nom as etablissement_nom
            FROM s4_bank_remboursement r
            JOIN s4_bank_pret p ON r.pret_id = p.id
            JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
            JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
            JOIN s4_bank_etablissement ef ON p.etablissement_id = ef.id
            ORDER BY r.date_echeance ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Effectue un paiement de remboursement avec montant automatique
     */
    public static function effectuerPaiement($remboursementId, $montantPaye = null)
    {
        $db = getDB();

        $db->beginTransaction();

        try {
            // Récupérer les informations du remboursement avec détails du prêt
            $stmt = $db->prepare("
                SELECT r.*, p.etablissement_id, p.montant_accorde, p.duree_mois, tp.taux_interet
                FROM s4_bank_remboursement r
                JOIN s4_bank_pret p ON r.pret_id = p.id
                JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
                WHERE r.id = ?
            ");
            $stmt->execute([$remboursementId]);
            $remboursement = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$remboursement) {
                throw new Exception('Remboursement non trouvé');
            }

            // AUTOMATISATION: Le montant payé est toujours égal au montant prévu (annuité)
            $montantPayeAutomatique = $remboursement['montant_prevu'];

            // Ignorer le paramètre $montantPaye passé et utiliser l'annuité calculée
            if ($montantPaye !== null && $montantPaye != $montantPayeAutomatique) {
                error_log("Tentative de paiement avec montant différent de l'annuité. Montant demandé: $montantPaye, Annuité: $montantPayeAutomatique");
            }

            // Calculer les pénalités si en retard
            $penalite = 0;
            $dateAujourdhui = new DateTime();
            $dateEcheance = new DateTime($remboursement['date_echeance']);

            if ($dateAujourdhui > $dateEcheance && $remboursement['statut'] !== 'paye') {
                $joursRetard = $dateAujourdhui->diff($dateEcheance)->days;
                $penalite = $remboursement['montant_prevu'] * 0.01 * $joursRetard; // 1% par jour de retard
            }

            $montantTotal = $montantPayeAutomatique + $penalite;

            // Le statut devient automatiquement 'paye' car le montant correspond à l'annuité
            $nouveauStatut = 'paye';

            // Mettre à jour le remboursement avec le montant automatique
            $stmt = $db->prepare("
                UPDATE s4_bank_remboursement 
                SET montant_paye = ?, penalite = ?, statut = ?, date_paiement = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$montantPayeAutomatique, $penalite, $nouveauStatut, $remboursementId]);

            // Récupérer le solde actuel de l'établissement
            $stmt = $db->prepare("SELECT fonds_disponibles FROM s4_bank_etablissement WHERE id = ?");
            $stmt->execute([$remboursement['etablissement_id']]);
            $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);

            // Mettre à jour les fonds de l'établissement
            $nouveauSolde = $etablissement['fonds_disponibles'] + $montantTotal;
            $stmt = $db->prepare("UPDATE s4_bank_etablissement SET fonds_disponibles = ? WHERE id = ?");
            $stmt->execute([$nouveauSolde, $remboursement['etablissement_id']]);

            // Enregistrer la transaction
            $stmt = $db->prepare("
                INSERT INTO s4_bank_transaction 
                (etablissement_id, pret_id, remboursement_id, type_transaction, montant, solde_avant, solde_apres, description) 
                VALUES (?, ?, ?, 'remboursement_recu', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $remboursement['etablissement_id'],
                $remboursement['pret_id'],
                $remboursementId,
                $montantTotal,
                $etablissement['fonds_disponibles'],
                $nouveauSolde,
                "Remboursement automatique - Échéance #" . $remboursement['numero_echeance'] . " (Annuité: " . $montantPayeAutomatique . ")"
            ]);

            // Si pénalité, enregistrer une transaction séparée
            if ($penalite > 0) {
                $stmt = $db->prepare("
                    INSERT INTO s4_bank_transaction 
                    (etablissement_id, pret_id, remboursement_id, type_transaction, montant, solde_avant, solde_apres, description) 
                    VALUES (?, ?, ?, 'penalite', ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $remboursement['etablissement_id'],
                    $remboursement['pret_id'],
                    $remboursementId,
                    $penalite,
                    $nouveauSolde - $penalite,
                    $nouveauSolde,
                    "Pénalité de retard - Échéance #" . $remboursement['numero_echeance']
                ]);
            }

            $db->commit();

            return [
                'success' => true,
                'montant_paye' => $montantPayeAutomatique,
                'penalite' => $penalite,
                'montant_total' => $montantTotal,
                'message' => 'Paiement automatique effectué pour le montant de l\'annuité'
            ];

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /**
     * Effectue un paiement de remboursement avec création automatique de transaction
     * et mise à jour du capital de l'établissement
     */
    public static function effectuerPaiementAvecTransaction($remboursementId, $montantPaye = null)
    {
        $db = getDB();
        $db->beginTransaction();

        try {
            // 1. Récupérer les informations complètes du remboursement
            $stmt = $db->prepare("
                SELECT r.*, p.etablissement_id, p.montant_accorde, p.duree_mois, p.etudiant_id,
                       tp.taux_interet, tp.nom as type_pret_nom,
                       e.nom as etudiant_nom, e.prenom as etudiant_prenom,
                       et.nom as etablissement_nom
                FROM s4_bank_remboursement r
                JOIN s4_bank_pret p ON r.pret_id = p.id
                JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
                JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
                JOIN s4_bank_etablissement et ON p.etablissement_id = et.id
                WHERE r.id = ? AND r.statut != 'paye'
            ");
            $stmt->execute([$remboursementId]);
            $remboursement = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$remboursement) {
                throw new Exception('Remboursement non trouvé ou déjà payé');
            }

            // 2. Calculer le montant à payer (automatique = annuité)
            $montantPayeAutomatique = $remboursement['montant_prevu'];

            // 3. Calculer les pénalités si en retard
            $penalite = 0;
            $dateAujourdhui = new DateTime();
            $dateEcheance = new DateTime($remboursement['date_echeance']);
            $joursRetard = 0;

            if ($dateAujourdhui > $dateEcheance) {
                $joursRetard = $dateAujourdhui->diff($dateEcheance)->days;
                $penalite = round($remboursement['montant_prevu'] * 0.01 * $joursRetard, 2);
            }

            $montantTotal = $montantPayeAutomatique + $penalite;

            // 4. Mettre à jour le remboursement
            $stmt = $db->prepare("
                UPDATE s4_bank_remboursement 
                SET montant_paye = ?, penalite = ?, statut = 'paye', date_paiement = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$montantPayeAutomatique, $penalite, $remboursementId]);

            // 5. Récupérer le solde actuel de l'établissement
            $stmt = $db->prepare("SELECT fonds_disponibles FROM s4_bank_etablissement WHERE id = ?");
            $stmt->execute([$remboursement['etablissement_id']]);
            $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$etablissement) {
                throw new Exception('Établissement non trouvé');
            }

            $soldeAvant = $etablissement['fonds_disponibles'];

            // 6. *** AUGMENTER LE CAPITAL DE L'ÉTABLISSEMENT ***
            $nouveauSolde = $soldeAvant + $montantTotal;
            $stmt = $db->prepare("
                UPDATE s4_bank_etablissement 
                SET fonds_disponibles = ?, date_modification = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$nouveauSolde, $remboursement['etablissement_id']]);

            // 7. *** CRÉER LA TRANSACTION PRINCIPALE ***
            $description = sprintf(
                "Remboursement reçu - Échéance #%d - %s %s - %s",
                $remboursement['numero_echeance'],
                $remboursement['etudiant_prenom'],
                $remboursement['etudiant_nom'],
                $remboursement['type_pret_nom']
            );

            $stmt = $db->prepare("
                INSERT INTO s4_bank_transaction 
                (etablissement_id, pret_id, remboursement_id, type_transaction, montant, 
                 solde_avant, solde_apres, description, date_transaction) 
                VALUES (?, ?, ?, 'remboursement_recu', ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $remboursement['etablissement_id'],
                $remboursement['pret_id'],
                $remboursementId,
                $montantPayeAutomatique,
                $soldeAvant,
                $nouveauSolde,
                $description
            ]);

            $transactionPrincipaleId = $db->lastInsertId();

            // 8. *** CRÉER TRANSACTION POUR PÉNALITÉS SI APPLICABLE ***
            if ($penalite > 0) {
                $descriptionPenalite = sprintf(
                    "Pénalité de retard - Échéance #%d - %d jours de retard",
                    $remboursement['numero_echeance'],
                    $joursRetard
                );

                $stmt = $db->prepare("
                    INSERT INTO s4_bank_transaction 
                    (etablissement_id, pret_id, remboursement_id, type_transaction, montant, 
                     solde_avant, solde_apres, description, date_transaction) 
                    VALUES (?, ?, ?, 'penalite_retard', ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $remboursement['etablissement_id'],
                    $remboursement['pret_id'],
                    $remboursementId,
                    $penalite,
                    $nouveauSolde - $penalite,
                    $nouveauSolde,
                    $descriptionPenalite
                ]);
            }

            // 9. Vérifier si le prêt est entièrement remboursé
            $stmt = $db->prepare("
                SELECT COUNT(*) as total_echeances, 
                       COUNT(CASE WHEN statut = 'paye' THEN 1 END) as echeances_payees
                FROM s4_bank_remboursement WHERE pret_id = ?
            ");
            $stmt->execute([$remboursement['pret_id']]);
            $statutPret = $stmt->fetch(PDO::FETCH_ASSOC);

            $pretEntierementRembourse = false;
            if ($statutPret['total_echeances'] == $statutPret['echeances_payees']) {
                // Marquer le prêt comme remboursé
                $stmt = $db->prepare("
                    UPDATE s4_bank_pret 
                    SET statut = 'rembourse', date_fin_remboursement = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$remboursement['pret_id']]);
                $pretEntierementRembourse = true;

                // Créer une transaction pour marquer la fin du prêt
                $stmt = $db->prepare("
                    INSERT INTO s4_bank_transaction 
                    (etablissement_id, pret_id, type_transaction, montant, 
                     solde_avant, solde_apres, description, date_transaction) 
                    VALUES (?, ?, 'pret_rembourse', 0, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $remboursement['etablissement_id'],
                    $remboursement['pret_id'],
                    $nouveauSolde,
                    $nouveauSolde,
                    "Prêt entièrement remboursé - " . $remboursement['etudiant_prenom'] . " " . $remboursement['etudiant_nom']
                ]);
            }

            // 10. Valider la transaction
            $db->commit();

            // 11. Log pour audit
            error_log(sprintf(
                "PAIEMENT EFFECTUÉ: Remboursement ID=%d, Montant=%s€, Pénalité=%s€, Total=%s€, Établissement=%d, Nouveau solde=%s€",
                $remboursementId,
                number_format($montantPayeAutomatique, 2),
                number_format($penalite, 2),
                number_format($montantTotal, 2),
                $remboursement['etablissement_id'],
                number_format($nouveauSolde, 2)
            ));

            return [
                'success' => true,
                'montant_paye' => $montantPayeAutomatique,
                'penalite' => $penalite,
                'montant_total' => $montantTotal,
                'jours_retard' => $joursRetard,
                'solde_avant' => $soldeAvant,
                'nouveau_solde' => $nouveauSolde,
                'pret_entierement_rembourse' => $pretEntierementRembourse,
                'transaction_principale_id' => $transactionPrincipaleId,
                'etablissement' => $remboursement['etablissement_nom'],
                'etudiant' => $remboursement['etudiant_prenom'] . ' ' . $remboursement['etudiant_nom'],
                'message' => 'Paiement automatique effectué avec succès. Transaction créée et capital de l\'établissement mis à jour.'
            ];

        } catch (Exception $e) {
            $db->rollback();
            error_log("ERREUR LORS DU PAIEMENT DE REMBOURSEMENT: " . $e->getMessage());
            throw new Exception("Erreur lors du paiement : " . $e->getMessage());
        }
    }

    /**
     * Calcule le détail d'une échéance (intérêts et capital)
     */
    public static function calculerDetailEcheance($pretId, $numeroEcheance)
    {
        $db = getDB();

        // Récupérer les informations du prêt
        $stmt = $db->prepare("
            SELECT p.montant_accorde, p.duree_mois, tp.taux_interet
            FROM s4_bank_pret p
            JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
            WHERE p.id = ?
        ");
        $stmt->execute([$pretId]);
        $pret = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pret) {
            throw new Exception('Prêt non trouvé');
        }

        $capital = $pret['montant_accorde'];
        $tauxMensuel = $pret['taux_interet'] / 100 / 12;
        $annuite = self::calculerAnnuite($capital, $pret['taux_interet'], $pret['duree_mois']);

        // Calculer le capital restant dû au début de cette échéance
        $capitalRestant = $capital;
        for ($i = 1; $i < $numeroEcheance; $i++) {
            $interets = $capitalRestant * $tauxMensuel;
            $capitalRembourse = $annuite - $interets;
            $capitalRestant -= $capitalRembourse;
        }

        // Calcul pour l'échéance demandée
        $interets = $capitalRestant * $tauxMensuel;
        $capitalRembourse = $annuite - $interets;

        return [
            'capital_restant_debut' => round($capitalRestant, 2),
            'interets' => round($interets, 2),
            'capital_rembourse' => round($capitalRembourse, 2),
            'capital_restant_fin' => round($capitalRestant - $capitalRembourse, 2)
        ];
    }

    /**
     * Récupère les remboursements en retard
     */
    public static function getRemboursementsEnRetard()
    {
        $db = getDB();
        $stmt = $db->query("
            SELECT r.*, p.montant_accorde, tp.taux_interet,
                   e.nom as etudiant_nom, e.prenom as etudiant_prenom, e.email as etudiant_email,
                   ef.nom as etablissement_nom,
                   DATEDIFF(CURDATE(), r.date_echeance) as jours_retard
            FROM s4_bank_remboursement r
            JOIN s4_bank_pret p ON r.pret_id = p.id
            JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
            JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
            JOIN s4_bank_etablissement ef ON p.etablissement_id = ef.id
            WHERE r.date_echeance < CURDATE() 
            AND r.statut IN ('en_attente', 'retard')
            ORDER BY r.date_echeance ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les remboursements non payés pour le select de paiement
     */
    public static function getRemboursementsNonPayes()
    {
        $db = getDB();
        $stmt = $db->query("
            SELECT r.id, r.numero_echeance, r.montant_prevu, r.date_echeance, 
                   r.statut, p.id as pret_id,
                   CONCAT(e.prenom, ' ', e.nom) as etudiant_nom,
                   tp.nom as type_pret_nom
            FROM s4_bank_remboursement r
            JOIN s4_bank_pret p ON r.pret_id = p.id
            JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
            JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
            WHERE r.statut IN ('en_attente', 'retard')
            ORDER BY r.date_echeance ASC, e.nom ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les prêts validés pour la simulation
     */
    public static function getPretsValides()
    {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT p.*, e.nom as etudiant_nom, e.prenom as etudiant_prenom, 
                   et.nom as etablissement_nom, tp.nom as type_pret
            FROM s4_bank_pret p
            JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
            JOIN s4_bank_etablissement et ON p.etablissement_id = et.id
            JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
            WHERE p.statut = 'actif'
            ORDER BY p.date_demande DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Simule un prêt avec tableau d'amortissement
     */
    public static function simulerPret($capital, $tauxAnnuel, $dureeMois)
    {
        $tauxMensuel = $tauxAnnuel / 100 / 12;
        $annuite = self::calculerAnnuite($capital, $tauxAnnuel, $dureeMois);

        $tableau = [];
        $capitalRestant = $capital;

        for ($i = 1; $i <= $dureeMois; $i++) {
            $interets = $capitalRestant * $tauxMensuel;
            $capitalRembourse = $annuite - $interets;

            // Ajustement pour la dernière échéance
            if ($i == $dureeMois) {
                $capitalRembourse = $capitalRestant;
                $annuite_ajustee = $capitalRembourse + $interets;
            } else {
                $annuite_ajustee = $annuite;
            }

            $tableau[] = [
                'numero_echeance' => $i,
                'capital_restant_debut' => round($capitalRestant, 2),
                'annuite' => round($annuite_ajustee, 2),
                'interets' => round($interets, 2),
                'capital_rembourse' => round($capitalRembourse, 2),
                'capital_restant_fin' => round($capitalRestant - $capitalRembourse, 2)
            ];

            $capitalRestant -= $capitalRembourse;
        }

        return [
            'simulation' => [
                'capital' => floatval($capital),
                'taux_annuel' => floatval($tauxAnnuel),
                'duree_mois' => intval($dureeMois),
                'annuite' => floatval($annuite),
                'montant_total' => floatval(round($annuite * $dureeMois, 2)),
                'cout_credit' => floatval(round(($annuite * $dureeMois) - $capital, 2))
            ],
            'tableau_amortissement' => $tableau
        ];
    }

    /**
     * Simule un prêt existant avec ses données réelles
     */
    public static function simulerPretExistant($pretId)
    {
        try {
            $db = getDB();

            // Vérifier que la connexion DB fonctionne
            if (!$db) {
                throw new Exception('Connexion à la base de données échouée');
            }

            // Récupérer les informations du prêt existant
            $stmt = $db->prepare("
                SELECT p.*, tp.taux_interet, tp.nom as type_pret_nom,
                       e.nom as etudiant_nom, e.prenom as etudiant_prenom,
                       et.nom as etablissement_nom
                FROM s4_bank_pret p
                JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
                JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
                JOIN s4_bank_etablissement et ON p.etablissement_id = et.id
                WHERE p.id = ? AND p.statut = 'actif'
            ");

            if (!$stmt) {
                throw new Exception('Erreur de préparation de la requête');
            }

            $executed = $stmt->execute([$pretId]);
            if (!$executed) {
                throw new Exception('Erreur d\'exécution de la requête');
            }

            $pret = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pret) {
                throw new Exception('Prêt non trouvé ou non actif (ID: ' . $pretId . ')');
            }

            // Vérifier que les champs nécessaires sont présents
            if (!isset($pret['montant_accorde']) || !isset($pret['taux_interet']) || !isset($pret['duree_mois'])) {
                throw new Exception('Données de prêt incomplètes');
            }

            // Utiliser la fonction de simulation générale avec les données du prêt
            $simulation = self::simulerPret(
                $pret['montant_accorde'],
                $pret['taux_interet'],
                $pret['duree_mois']
            );

            // Ajouter les informations du prêt à la simulation - CORRECTION ICI
            $simulation['pret_info'] = [
                'id' => $pret['id'],
                'etudiant' => ($pret['etudiant_prenom'] ?? '') . ' ' . ($pret['etudiant_nom'] ?? ''),
                'etablissement' => $pret['etablissement_nom'] ?? '',
                'type_pret' => $pret['type_pret_nom'] ?? '',
                'date_demande' => $pret['date_demande'] ?? null,
                'date_approbation' => $pret['date_approbation'] ?? null,
                'statut' => $pret['statut'] ?? ''
            ];

            return $simulation;

        } catch (PDOException $e) {
            error_log("Erreur PDO dans simulerPretExistant: " . $e->getMessage());
            throw new Exception('Erreur de base de données: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log("Erreur dans simulerPretExistant: " . $e->getMessage());
            throw $e;
        }
    }
}
