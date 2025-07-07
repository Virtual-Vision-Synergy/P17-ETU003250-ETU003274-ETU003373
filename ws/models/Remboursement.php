<?php

class RemboursementService
{
    /**
     * Calcule l'annuité constante pour un prêt
     * Formule: A = C * [i * (1+i)^n] / [(1+i)^n - 1]
     * où A = annuité, C = capital, i = taux d'intérêt mensuel, n = nombre de mensualités
     */
    public static function calculerAnnuite($capital, $tauxAnnuel, $dureeMois)
    {
        $tauxMensuel = $tauxAnnuel / 100 / 12; // Conversion du taux annuel en taux mensuel

        if ($tauxMensuel == 0) {
            return $capital / $dureeMois; // Si pas d'intérêts, remboursement linéaire
        }

        $facteur = pow(1 + $tauxMensuel, $dureeMois);
        $annuite = $capital * ($tauxMensuel * $facteur) / ($facteur - 1);

        return round($annuite, 2);
    }

    /**
     * Génère le tableau d'amortissement complet pour un prêt
     */
    public static function genererTableauAmortissement($pretId, $capital, $tauxAnnuel, $dureeMois, $dateDebut)
    {
        $db = getDB();
        $tauxMensuel = $tauxAnnuel / 100 / 12;
        $annuite = self::calculerAnnuite($capital, $tauxAnnuel, $dureeMois);

        $capitalRestant = $capital;
        $dateEcheance = new DateTime($dateDebut);

        $db->beginTransaction();

        try {
            // Supprimer les anciens remboursements s'ils existent
            $stmt = $db->prepare("DELETE FROM s4_bank_remboursement WHERE pret_id = ?");
            $stmt->execute([$pretId]);

            for ($i = 1; $i <= $dureeMois; $i++) {
                $dateEcheance->add(new DateInterval('P1M')); // Ajouter 1 mois

                // Calcul des intérêts pour cette période
                $interets = $capitalRestant * $tauxMensuel;

                // Calcul du capital remboursé
                $capitalRembourse = $annuite - $interets;

                // Ajustement pour la dernière échéance (pour éviter les erreurs d'arrondi)
                if ($i == $dureeMois) {
                    $capitalRembourse = $capitalRestant;
                    $annuite = $capitalRembourse + $interets;
                }

                // Insérer l'échéance
                $stmt = $db->prepare("
                    INSERT INTO s4_bank_remboursement 
                    (pret_id, numero_echeance, montant_prevu, date_echeance, statut) 
                    VALUES (?, ?, ?, ?, 'en_attente')
                ");
                $stmt->execute([
                    $pretId,
                    $i,
                    round($annuite, 2),
                    $dateEcheance->format('Y-m-d')
                ]);

                $capitalRestant -= $capitalRembourse;
            }

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
     * Effectue un paiement de remboursement
     */
    public static function effectuerPaiement($remboursementId, $montantPaye)
    {
        $db = getDB();

        $db->beginTransaction();

        try {
            // Récupérer les informations du remboursement
            $stmt = $db->prepare("
                SELECT r.*, p.etablissement_id, p.montant_accorde, tp.taux_interet
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

            // Calculer les pénalités si en retard
            $penalite = 0;
            $dateAujourdhui = new DateTime();
            $dateEcheance = new DateTime($remboursement['date_echeance']);

            if ($dateAujourdhui > $dateEcheance && $remboursement['statut'] !== 'paye') {
                $joursRetard = $dateAujourdhui->diff($dateEcheance)->days;
                $penalite = $remboursement['montant_prevu'] * 0.01 * $joursRetard; // 1% par jour de retard
            }

            $montantTotal = $montantPaye + $penalite;
            $nouveauStatut = ($montantTotal >= $remboursement['montant_prevu']) ? 'paye' : 'en_attente';

            // Mettre à jour le remboursement
            $stmt = $db->prepare("
                UPDATE s4_bank_remboursement 
                SET montant_paye = ?, penalite = ?, statut = ?, date_paiement = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$montantPaye, $penalite, $nouveauStatut, $remboursementId]);

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
                "Remboursement reçu - Échéance #" . $remboursement['numero_echeance']
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
            return true;

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
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
            'capital_rembourse' => round($capitalRemburse, 2),
            'annuite' => round($annuite, 2),
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
}
