<?php

class EtablissementService {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getAllEtablissements() {
        $stmt = $this->db->query("SELECT * FROM s4_bank_etablissement");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEtablissementById($id) {
        $stmt = $this->db->prepare("SELECT * FROM s4_bank_etablissement WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createEtablissement($data) {
        // Validation des données
        $this->validateEtablissementData($data);

        $stmt = $this->db->prepare("
            INSERT INTO s4_bank_etablissement 
            (nom, adresse, telephone, email, fonds_disponibles) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data->nom,
            $data->adresse ?? null,
            $data->telephone ?? null,
            $data->email ?? null,
            $data->fonds_disponibles ?? 0
        ]);

        return $this->db->lastInsertId();
    }

    public function updateEtablissement($id, $data) {
        // Vérifier si l'établissement existe
        if (!$this->getEtablissementById($id)) {
            throw new InvalidArgumentException("Établissement non trouvé");
        }

        // Validation des données
        $this->validateEtablissementData($data);

        $stmt = $this->db->prepare("
            UPDATE s4_bank_etablissement SET 
            nom = ?, adresse = ?, telephone = ?, email = ?, fonds_disponibles = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data->nom,
            $data->adresse,
            $data->telephone,
            $data->email,
            $data->fonds_disponibles,
            $id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function deleteEtablissement($id) {
        // Vérifier si l'établissement a des prêts actifs
        if ($this->hasActiveLoans($id)) {
            throw new InvalidArgumentException("Impossible de supprimer un établissement ayant des prêts actifs");
        }

        $stmt = $this->db->prepare("DELETE FROM s4_bank_etablissement WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public function updateFonds($id, $nouveauMontant) {
        $stmt = $this->db->prepare("UPDATE s4_bank_etablissement SET fonds_disponibles = ? WHERE id = ?");
        $stmt->execute([$nouveauMontant, $id]);

        return $stmt->rowCount() > 0;
    }

    public function addFonds($id, $montant) {
        $stmt = $this->db->prepare("
            UPDATE s4_bank_etablissement 
            SET fonds_disponibles = fonds_disponibles + ? 
            WHERE id = ?
        ");
        $stmt->execute([$montant, $id]);

        return $stmt->rowCount() > 0;
    }

    public function deductFonds($id, $montant) {
        // Vérifier les fonds disponibles
        $etablissement = $this->getEtablissementById($id);
        if (!$etablissement) {
            throw new InvalidArgumentException("Établissement non trouvé");
        }

        if ($etablissement['fonds_disponibles'] < $montant) {
            throw new InvalidArgumentException("Fonds insuffisants");
        }

        $stmt = $this->db->prepare("
            UPDATE s4_bank_etablissement 
            SET fonds_disponibles = fonds_disponibles - ? 
            WHERE id = ?
        ");
        $stmt->execute([$montant, $id]);

        return $stmt->rowCount() > 0;
    }

    public function getStatistiques($id) {
        $etablissement = $this->getEtablissementById($id);
        if (!$etablissement) {
            throw new InvalidArgumentException("Établissement non trouvé");
        }

        // Nombre de prêts total
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM s4_bank_pret WHERE etablissement_id = ?");
        $stmt->execute([$id]);
        $totalPrets = $stmt->fetchColumn();

        // Montant total prêté
        $stmt = $this->db->prepare("SELECT SUM(montant_accorde) FROM s4_bank_pret WHERE etablissement_id = ?");
        $stmt->execute([$id]);
        $montantTotal = $stmt->fetchColumn() ?? 0;

        // Prêts actifs
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM s4_bank_pret 
            WHERE etablissement_id = ? AND statut IN ('en_cours', 'approuve')
        ");
        $stmt->execute([$id]);
        $pretsActifs = $stmt->fetchColumn();

        return [
            'etablissement' => $etablissement,
            'total_prets' => $totalPrets,
            'montant_total_prete' => $montantTotal,
            'prets_actifs' => $pretsActifs,
            'fonds_disponibles' => $etablissement['fonds_disponibles']
        ];
    }

    private function validateEtablissementData($data) {
        if (empty($data->nom)) {
            throw new InvalidArgumentException("Le nom de l'établissement est obligatoire");
        }

        if (isset($data->email) && !empty($data->email) && !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("L'email n'est pas valide");
        }

        if (isset($data->fonds_disponibles) && $data->fonds_disponibles < 0) {
            throw new InvalidArgumentException("Les fonds disponibles ne peuvent pas être négatifs");
        }

        if (isset($data->telephone) && !empty($data->telephone) && !preg_match('/^[0-9+\-\s()]+$/', $data->telephone)) {
            throw new InvalidArgumentException("Le numéro de téléphone n'est pas valide");
        }
    }

    private function hasActiveLoans($etablissementId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM s4_bank_pret 
            WHERE etablissement_id = ? AND statut IN ('en_attente', 'approuve', 'en_cours')
        ");
        $stmt->execute([$etablissementId]);

        return $stmt->fetchColumn() > 0;
    }
}
