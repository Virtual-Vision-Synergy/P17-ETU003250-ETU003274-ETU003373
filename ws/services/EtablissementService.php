<?php

class EtablissementService {

    public static function getAllEtablissements() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM s4_bank_etablissement ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEtablissementById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM s4_bank_etablissement WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createEtablissement($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO s4_bank_etablissement (nom, adresse, telephone, email, fonds_disponibles) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->adresse ?? null,
            $data->telephone ?? null,
            $data->email ?? null,
            $data->fonds_disponibles ?? 0
        ]);
        return $db->lastInsertId();
    }

    public static function depot($id, $montant, $description = null) {
        if ($montant <= 0) {
            throw new InvalidArgumentException('Le montant doit être positif');
        }

        $db = getDB();
        $db->beginTransaction();

        try {
            // Récupérer le solde actuel
            $etablissement = self::getEtablissementById($id);
            if (!$etablissement) {
                throw new Exception('Établissement non trouvé');
            }

            $soldeAvant = $etablissement['fonds_disponibles'];
            $soldeApres = $soldeAvant + $montant;

            // Mettre à jour les fonds
            $stmt = $db->prepare("UPDATE s4_bank_etablissement SET fonds_disponibles = ? WHERE id = ?");
            $stmt->execute([$soldeApres, $id]);

            // Enregistrer la transaction
            $stmt = $db->prepare("INSERT INTO s4_bank_transaction (etablissement_id, type_transaction, montant, solde_avant, solde_apres, description) VALUES (?, 'depot', ?, ?, ?, ?)");
            $stmt->execute([$id, $montant, $soldeAvant, $soldeApres, $description ?? 'Dépôt de fonds']);

            $db->commit();
            return ['ancien_solde' => $soldeAvant, 'nouveau_solde' => $soldeApres];
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public static function validateEtablissementData($data) {
        $errors = [];

        if (empty($data->nom)) {
            $errors[] = "Le nom de l'établissement est requis";
        }

        if (!empty($data->email) && !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide";
        }

        if (!empty($data->fonds_disponibles) && (!is_numeric($data->fonds_disponibles) || $data->fonds_disponibles < 0)) {
            $errors[] = "Les fonds disponibles doivent être un nombre positif";
        }

        return $errors;
    }
}
