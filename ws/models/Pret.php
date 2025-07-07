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

    public static function getPretById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT p.*,
                   e.nom as etudiant_nom, e.prenom as etudiant_prenom, e.email as etudiant_email,
                   tp.nom as type_pret_nom, tp.description as type_description,
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

        // Vérifier si l'établissement a suffisamment de fonds
        $etablissement = EtablissementService::getEtablissementById($data->etablissement_id);
        if (!$etablissement) {
            throw new Exception('Établissement non trouvé');
        }

        if ($etablissement['fonds_disponibles'] < $data->montant) {
            throw new Exception('Fonds insuffisants dans l\'établissement');
        }

        $db->beginTransaction();

        try {
            // Créer le prêt
            $stmt = $db->prepare("INSERT INTO s4_bank_pret (etudiant_id, type_pret_id, etablissement_id, montant, duree_mois, taux_interet, statut) VALUES (?, ?, ?, ?, ?, ?, 'en_attente')");
            $stmt->execute([
                $data->etudiant_id,
                $data->type_pret_id,
                $data->etablissement_id,
                $data->montant,
                $data->duree_mois,
                $data->taux_interet
            ]);

            $pretId = $db->lastInsertId();

            // Débiter les fonds de l'établissement
            $nouveauSolde = $etablissement['fonds_disponibles'] - $data->montant;
            $stmt = $db->prepare("UPDATE s4_bank_etablissement SET fonds_disponibles = ? WHERE id = ?");
            $stmt->execute([$nouveauSolde, $data->etablissement_id]);

            // Enregistrer la transaction
            $stmt = $db->prepare("INSERT INTO s4_bank_transaction (etablissement_id, type_transaction, montant, solde_avant, solde_apres, description) VALUES (?, 'pret', ?, ?, ?, ?)");
            $stmt->execute([
                $data->etablissement_id,
                $data->montant,
                $etablissement['fonds_disponibles'],
                $nouveauSolde,
                "Prêt accordé - ID: $pretId"
            ]);

            $db->commit();
            return $pretId;

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

        if (empty($data->montant) || !is_numeric($data->montant) || $data->montant <= 0) {
            $errors[] = "Le montant doit être un nombre positif";
        }

        if (empty($data->duree_mois) || !is_numeric($data->duree_mois) || $data->duree_mois <= 0) {
            $errors[] = "La durée doit être un nombre positif de mois";
        }

        if (empty($data->taux_interet) || !is_numeric($data->taux_interet) || $data->taux_interet <= 0) {
            $errors[] = "Le taux d'intérêt doit être un nombre positif";
        }

        return $errors;
    }
}
