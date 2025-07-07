<?php

class EtudiantService {

    public static function getAllEtudiants() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM s4_bank_etudiant ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEtudiantById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM s4_bank_etudiant WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createEtudiant($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO s4_bank_etudiant (nom, prenom, email, age, telephone, adresse) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->prenom,
            $data->email,
            $data->age,
            $data->telephone ?? null,
            $data->adresse ?? null
        ]);
        return $db->lastInsertId();
    }

    public static function updateEtudiant($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE s4_bank_etudiant SET nom = ?, prenom = ?, email = ?, age = ?, telephone = ?, adresse = ? WHERE id = ?");
        return $stmt->execute([
            $data->nom,
            $data->prenom,
            $data->email,
            $data->age,
            $data->telephone ?? null,
            $data->adresse ?? null,
            $id
        ]);
    }

    public static function deleteEtudiant($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM s4_bank_etudiant WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function validateEtudiantData($data) {
        $errors = [];

        if (empty($data->nom)) {
            $errors[] = "Le nom est requis";
        }

        if (empty($data->prenom)) {
            $errors[] = "Le prénom est requis";
        }

        if (empty($data->email)) {
            $errors[] = "L'email est requis";
        } elseif (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide";
        }

        if (empty($data->age) || !is_numeric($data->age) || $data->age < 16) {
            $errors[] = "L'âge doit être un nombre supérieur ou égal à 16";
        }

        return $errors;
    }
}
