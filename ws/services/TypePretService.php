<?php

class TypePretService {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getAllTypesPrets() {
        $stmt = $this->db->query("SELECT * FROM s4_bank_type_pret WHERE actif = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTypePretById($id) {
        $stmt = $this->db->prepare("SELECT * FROM s4_bank_type_pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTypePret($data) {
        // Validation des données
        $this->validateTypePretData($data);

        $stmt = $this->db->prepare("
            INSERT INTO s4_bank_type_pret 
            (nom, description, taux_interet, duree_max_mois, montant_min, montant_max) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data->nom,
            $data->description,
            $data->taux_interet,
            $data->duree_max_mois,
            $data->montant_min ?? 0,
            $data->montant_max ?? null
        ]);

        return $this->db->lastInsertId();
    }

    public function updateTypePret($id, $data) {
        // Vérifier si le type de prêt existe
        if (!$this->getTypePretById($id)) {
            throw new InvalidArgumentException("Type de prêt non trouvé");
        }

        // Validation des données
        $this->validateTypePretData($data);

        $stmt = $this->db->prepare("
            UPDATE s4_bank_type_pret SET 
            nom = ?, description = ?, taux_interet = ?, duree_max_mois = ?, 
            montant_min = ?, montant_max = ?, actif = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data->nom,
            $data->description,
            $data->taux_interet,
            $data->duree_max_mois,
            $data->montant_min ?? 0,
            $data->montant_max ?? null,
            $data->actif ?? 1,
            $id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function deleteTypePret($id) {
        // Vérifier si le type de prêt est utilisé
        if ($this->isTypeUsed($id)) {
            throw new InvalidArgumentException("Impossible de supprimer un type de prêt utilisé dans des prêts existants");
        }

        $stmt = $this->db->prepare("DELETE FROM s4_bank_type_pret WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public function deactivateTypePret($id) {
        $stmt = $this->db->prepare("UPDATE s4_bank_type_pret SET actif = 0 WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public function validateLoanAmount($typePretId, $montant) {
        $typePret = $this->getTypePretById($typePretId);

        if (!$typePret) {
            throw new InvalidArgumentException("Type de prêt non trouvé");
        }

        if ($montant < $typePret['montant_min']) {
            throw new InvalidArgumentException("Montant inférieur au minimum autorisé ({$typePret['montant_min']})");
        }

        if ($typePret['montant_max'] && $montant > $typePret['montant_max']) {
            throw new InvalidArgumentException("Montant supérieur au maximum autorisé ({$typePret['montant_max']})");
        }

        return true;
    }

    public function validateLoanDuration($typePretId, $dureeMois) {
        $typePret = $this->getTypePretById($typePretId);

        if (!$typePret) {
            throw new InvalidArgumentException("Type de prêt non trouvé");
        }

        if ($dureeMois > $typePret['duree_max_mois']) {
            throw new InvalidArgumentException("Durée supérieure au maximum autorisé ({$typePret['duree_max_mois']} mois)");
        }

        return true;
    }

    private function validateTypePretData($data) {
        if (empty($data->nom)) {
            throw new InvalidArgumentException("Le nom du type de prêt est obligatoire");
        }

        if ($data->taux_interet < 0 || $data->taux_interet > 100) {
            throw new InvalidArgumentException("Le taux d'intérêt doit être entre 0 et 100%");
        }

        if ($data->duree_max_mois <= 0) {
            throw new InvalidArgumentException("La durée maximale doit être positive");
        }

        if (isset($data->montant_min) && $data->montant_min < 0) {
            throw new InvalidArgumentException("Le montant minimum ne peut pas être négatif");
        }

        if (isset($data->montant_max) && isset($data->montant_min) && $data->montant_max < $data->montant_min) {
            throw new InvalidArgumentException("Le montant maximum ne peut pas être inférieur au montant minimum");
        }
    }

    private function isTypeUsed($typePretId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM s4_bank_pret WHERE type_pret_id = ?");
        $stmt->execute([$typePretId]);

        return $stmt->fetchColumn() > 0;
    }
}
