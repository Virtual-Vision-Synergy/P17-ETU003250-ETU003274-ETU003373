<?php

class TypePretService {

    public static function getAllTypesPrets() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM s4_bank_type_pret ORDER BY taux_interet");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTypePretById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM s4_bank_type_pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createTypePret($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO s4_bank_type_pret (nom, description, taux_interet, duree_max_mois, montant_min, montant_max, actif) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->description ?? null,
            $data->taux_interet,
            $data->duree_max_mois,
            $data->montant_min ?? 0,
            $data->montant_max ?? null,
            $data->actif ?? true
        ]);
        return $db->lastInsertId();
    }

    public static function updateTypePret($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE s4_bank_type_pret SET nom = ?, description = ?, taux_interet = ?, duree_max_mois = ?, montant_min = ?, montant_max = ?, actif = ? WHERE id = ?");
        return $stmt->execute([
            $data->nom,
            $data->description,
            $data->taux_interet,
            $data->duree_max_mois,
            $data->montant_min,
            $data->montant_max,
            $data->actif,
            $id
        ]);
    }

    public static function deleteTypePret($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM s4_bank_type_pret WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function validateTypePretData($data) {
        $errors = [];

        if (empty($data->nom)) {
            $errors[] = "Le nom du type de prêt est requis";
        }

        if (empty($data->taux_interet) || !is_numeric($data->taux_interet) || $data->taux_interet <= 0) {
            $errors[] = "Le taux d'intérêt doit être un nombre positif";
        }

        if (empty($data->duree_max_mois) || !is_numeric($data->duree_max_mois) || $data->duree_max_mois <= 0) {
            $errors[] = "La durée maximale doit être un nombre positif de mois";
        }

        if (!empty($data->montant_min) && (!is_numeric($data->montant_min) || $data->montant_min < 0)) {
            $errors[] = "Le montant minimum doit être un nombre positif";
        }

        if (!empty($data->montant_max) && (!is_numeric($data->montant_max) || $data->montant_max <= 0)) {
            $errors[] = "Le montant maximum doit être un nombre positif";
        }

        if (!empty($data->montant_min) && !empty($data->montant_max) && $data->montant_min > $data->montant_max) {
            $errors[] = "Le montant minimum ne peut pas être supérieur au montant maximum";
        }

        return $errors;
    }
}
