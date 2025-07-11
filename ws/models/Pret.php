<?php

class PretService
{

    public static function getAllPrets()
    {
        $db = getDB();
        $stmt = $db->query("
            SELECT p.*,
                   e.nom as etudiant_nom, e.prenom as etudiant_prenom, e.email as etudiant_email,
                   tp.nom as type_pret_nom, tp.taux_interet as type_taux,
                   ef.nom as etablissement_nom
            FROM s4_bank_pret p
            JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
            JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
            JOIN s4_bank_etablissement ef ON p.etablissement_id = ef.id
            ORDER BY p.date_demande DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllPretsInProcess()
    {
        $db = getDB();
        $stmt = $db->query("
            SELECT p.*,
                   e.nom as etudiant_nom, e.prenom as etudiant_prenom, e.email as etudiant_email,
                   tp.nom as type_pret_nom, tp.taux_interet as type_taux,
                   ef.nom as etablissement_nom
            FROM s4_bank_pret p
            JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
            JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
            JOIN s4_bank_etablissement ef ON p.etablissement_id = ef.id
            WHERE statut = 'en_attente'
            ORDER BY p.date_demande DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPretById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT p.*,
                   e.nom as etudiant_nom, e.prenom as etudiant_prenom, e.email as etudiant_email,
                   tp.nom as type_pret_nom, tp.description as type_description, tp.taux_interet as type_taux,
                   ef.nom as etablissement_nom
            FROM s4_bank_pret p
            JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
            JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
            JOIN s4_bank_etablissement ef ON p.etablissement_id = ef.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createPret($data)
    {
        $db = getDB();

        // Validation des données
        $errors = self::validatePretData($data);
        if (!empty($errors)) {
            throw new InvalidArgumentException(implode(', ', $errors));
        }

        // Vérifier si l'établissement existe
        $etablissement = EtablissementService::getEtablissementById($data->etablissement_id);
        if (!$etablissement) {
            throw new Exception('Établissement non trouvé');
        }

        $db->beginTransaction();

        try {
            // Créer le prêt avec la colonne assurance_pourcentage
            $stmt = $db->prepare("INSERT INTO s4_bank_pret (etudiant_id, type_pret_id, etablissement_id, montant_demande, montant_accorde, duree_mois, mensualite, montant_total, assurance_pourcentage, delai, statut, date_approbation, date_debut, date_fin_prevue) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente', NULL, NULL, NULL)");
            $stmt->execute([
                $data->etudiant_id,
                $data->type_pret_id,
                $data->etablissement_id,
                $data->montant_demande,
                0.0, // montant_accorde sera défini lors de l'approbation
                $data->duree_mois,
                0.0, // mensualite sera calculée lors de l'approbation
                0.0, // montant_total sera calculé lors de l'approbation
                $data->assurance_pourcentage ?? 0.0,
                $data->delai ?? 0,
            ]);

            $pretId = $db->lastInsertId();
            $db->commit();
            return $pretId;

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public static function calculMensualite($montant, $taux, $duree, $assurance_pourcentage = 0)
    {
        // Calculer l'annuité de base (capital + intérêts)
        $annuite_base = $montant * ($taux / 100 / 12) / (1 - pow(1 + $taux / 100 / 12, -$duree));

        // Calculer l'assurance mensuelle (% du capital initial)
        $assurance_mensuelle = $montant * ($assurance_pourcentage / 100 / 12);

        // Retourner l'annuité totale (annuité + assurance mensuelle)
        return $annuite_base + $assurance_mensuelle;
    }

    public static function calculMontantAssuranceMensuelle($montant, $pourcentageAssurance)
    {
        return $montant * ($pourcentageAssurance / 100 / 12);
    }

    public static function calculMontantAssuranceTotal($montant, $pourcentageAssurance, $dureeMois)
    {
        $assurance_mensuelle = self::calculMontantAssuranceMensuelle($montant, $pourcentageAssurance);
        return $assurance_mensuelle * $dureeMois;
    }

    // Ancienne fonction gardée pour compatibilité mais dépréciée
    public static function calculMontantAssurance($montant, $pourcentageAssurance)
    {
        // Cette fonction est dépréciée, utiliser calculMontantAssuranceTotal() à la place
        return $montant * ($pourcentageAssurance / 100);
    }

    public static function approvePret($id, $data)
    {
        $db = getDB();
        // Validation des données
//        $errors = self::validatePretData($data);
        if (!empty($errors)) {
            throw new InvalidArgumentException(implode(', ', $errors));
        }

        $etablissement = EtablissementService::getEtablissementById($data["etablissement_id"]);
        if (!$etablissement) {
            throw new Exception('Établissement non trouvé');
        }

        $db->beginTransaction();

        try {
            // Récupérer les informations du type de prêt pour le taux d'intérêt
            $stmt = $db->prepare("
                SELECT tp.taux_interet 
                FROM s4_bank_pret p 
                JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id 
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            $pretInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pretInfo) {
                throw new Exception('Prêt non trouvé');
            }

            // Mettre à jour le prêt
            $stmt = $db->prepare("UPDATE s4_bank_pret SET montant_accorde = ?, mensualite = ?, montant_total = ?, statut = ?, date_approbation = ?, date_debut = ?, date_fin_prevue = ? WHERE id = ?");
            $stmt->execute([
                $data["montant_accorde"],
                $data["mensualite"],
                $data["montant_total"],
                $data["statut"],
                $data["date_approbation"],
                $data["date_debut"],
                $data["date_fin_prevue"],
                $id
            ]);

            // Si le prêt est approuvé, générer automatiquement le tableau d'amortissement
            if ($data["statut"] === 'actif' && isset($data["date_debut"])) {


                // Débiter les fonds de l'établissement
                $nouveauSolde = $etablissement['fonds_disponibles'] - $data["montant_accorde"];
                $stmt = $db->prepare("UPDATE s4_bank_etablissement SET fonds_disponibles = ? WHERE id = ?");
                $stmt->execute([$nouveauSolde, $etablissement["id"]]);

                // Enregistrer la transaction
                $stmt = $db->prepare("INSERT INTO s4_bank_transaction (etablissement_id, type_transaction, montant, solde_avant, solde_apres, description) VALUES (?, 'pret_accorde', ?, ?, ?, ?)");
                $stmt->execute([
                    $etablissement["id"],
                    $data["montant_accorde"],
                    $etablissement['fonds_disponibles'],
                    $nouveauSolde,
                    "Prêt accordé - ID: $id"
                ]);

            }


            $db->commit();
            if ($data["statut"] === 'actif' && isset($data["date_debut"]))
                RemboursementService::genererTableauAmortissement(
                    $id,
                    $data["montant_accorde"],
                    $pretInfo['taux_interet'],
                    $data["duree_mois"] ?? 12,
                    $data["date_debut"],
                    $data["delai"] ?? 0
                );
            return true;

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public static function validatePretData($data)
    {
        $errors = [];

        if (empty($data->etudiant_id) || !is_numeric($data->etudiant_id)) {
            $errors[] = "L'ID de l'étudiant est requis";
        }

        if (empty($data->type_pret_id) || !is_numeric($data->type_pret_id)) {
            $errors[] = "L'ID du type de prêt est requis";
        }

        if (empty($data->etablissement_id) || !is_numeric($data->etablissement_id)) {
            $errors[] = "L'ID de l'établissement est requis";
        }

        if (empty($data->montant_demande) || !is_numeric($data->montant_demande) || $data->montant_demande <= 0) {
            $errors[] = "Le montant demandé doit être un nombre positif";
        }

        if (empty($data->duree_mois) || !is_numeric($data->duree_mois) || $data->duree_mois <= 0) {
            $errors[] = "La durée doit être un nombre positif de mois";
        }

        // Validation du pourcentage d'assurance (optionnel)
        if (isset($data->assurance_pourcentage) && $data->assurance_pourcentage !== null) {
            if (!is_numeric($data->assurance_pourcentage) || $data->assurance_pourcentage < 0 || $data->assurance_pourcentage > 100) {
                $errors[] = "Le pourcentage d'assurance doit être un nombre entre 0 et 100";
            }
        }

        return $errors;
    }

    public static function validateApprovalData($data)
    {
        $errors = [];

        if (empty($data->montant_accorde) || !is_numeric($data->montant_accorde) || $data->montant_accorde <= 0) {
            $errors[] = "Le montant accordé doit être un nombre positif";
        }

        if (empty($data->mensualite) || !is_numeric($data->mensualite) || $data->mensualite <= 0) {
            $errors[] = "La mensualité doit être un nombre positif";
        }

        if (empty($data->montant_total) || !is_numeric($data->montant_total) || $data->montant_total <= 0) {
            $errors[] = "Le montant total doit être un nombre positif";
        }

        return $errors;
    }
}
