<?php

class EtudiantService {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getAllEtudiants() {
        $stmt = $this->db->query("SELECT * FROM s4_bank_etudiant");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEtudiantById($id) {
        $stmt = $this->db->prepare("SELECT * FROM s4_bank_etudiant WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createEtudiant($data) {
        // Validation des données
        if (empty($data->nom) || empty($data->prenom) || empty($data->email)) {
            throw new InvalidArgumentException("Les champs nom, prénom et email sont obligatoires");
        }

        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("L'email n'est pas valide");
        }

        // Vérifier si l'email existe déjà
        if ($this->emailExists($data->email)) {
            throw new InvalidArgumentException("Cet email est déjà utilisé");
        }

        $stmt = $this->db->prepare("INSERT INTO s4_bank_etudiant (nom, prenom, email, age) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age ?? null]);

        return $this->db->lastInsertId();
    }

    public function updateEtudiant($id, $data) {
        // Vérifier si l'étudiant existe
        if (!$this->getEtudiantById($id)) {
            throw new InvalidArgumentException("Étudiant non trouvé");
        }

        // Validation des données
        if (isset($data->email) && !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("L'email n'est pas valide");
        }

        // Vérifier si l'email existe déjà (sauf pour l'étudiant actuel)
        if (isset($data->email) && $this->emailExists($data->email, $id)) {
            throw new InvalidArgumentException("Cet email est déjà utilisé");
        }

        $stmt = $this->db->prepare("UPDATE s4_bank_etudiant SET nom = ?, prenom = ?, email = ?, age = ? WHERE id = ?");
        $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age, $id]);

        return $stmt->rowCount() > 0;
    }

    public function deleteEtudiant($id) {
        // Vérifier si l'étudiant a des prêts actifs
        if ($this->hasActiveLoans($id)) {
            throw new InvalidArgumentException("Impossible de supprimer un étudiant ayant des prêts actifs");
        }

        $stmt = $this->db->prepare("DELETE FROM s4_bank_etudiant WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    private function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM s4_bank_etudiant WHERE email = ?";
        $params = [$email];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    private function hasActiveLoans($etudiantId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM s4_bank_pret WHERE etudiant_id = ? AND statut IN ('en_attente', 'approuve', 'en_cours')");
        $stmt->execute([$etudiantId]);

        return $stmt->fetchColumn() > 0;
    }
}
