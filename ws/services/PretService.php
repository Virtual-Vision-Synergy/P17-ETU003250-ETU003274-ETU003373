<?php

class PretService {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getAllPrets() {
        $stmt = $this->db->query("
            SELECT p.*, e.nom, e.prenom, e.email, tp.nom as type_pret_nom, 
                   et.nom as etablissement_nom
            FROM s4_bank_pret p
            JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
            JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
            JOIN s4_bank_etablissement et ON p.etablissement_id = et.id
            ORDER BY p.date_demande DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPretById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, e.nom, e.prenom, e.email, tp.nom as type_pret_nom, 
                   et.nom as etablissement_nom
            FROM s4_bank_pret p
            JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
            JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
            JOIN s4_bank_etablissement et ON p.etablissement_id = et.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createPret($data) {
        // Validation des données
        $this->validatePretData($data);

        // Vérifier la disponibilité des fonds
        if (!$this->checkFundsAvailability($data->etablissement_id, $data->montant_accorde)) {
            throw new InvalidArgumentException("Fonds insuffisants dans l'établissement");
        }

        // Calcul de la mensualité et du montant total
        $calculations = $this->calculateLoanPayments($data->montant_accorde, $data->taux_applique, $data->duree_mois);

        $stmt = $this->db->prepare("
            INSERT INTO s4_bank_pret 
            (etudiant_id, type_pret_id, etablissement_id, montant_demande, montant_accorde, 
             taux_applique, duree_mois, mensualite, montant_total, statut) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data->etudiant_id,
            $data->type_pret_id,
            $data->etablissement_id,
            $data->montant_demande,
            $data->montant_accorde,
            $data->taux_applique,
            $data->duree_mois,
            $calculations['mensualite'],
            $calculations['montant_total'],
            $data->statut ?? 'en_attente'
        ]);

        return $this->db->lastInsertId();
    }

    public function updatePret($id, $data) {
        // Vérifier si le prêt existe
        if (!$this->getPretById($id)) {
            throw new InvalidArgumentException("Prêt non trouvé");
        }

        // Validation des données
        $this->validatePretData($data);

        // Recalcul si nécessaire
        $calculations = $this->calculateLoanPayments($data->montant_accorde, $data->taux_applique, $data->duree_mois);

        $stmt = $this->db->prepare("
            UPDATE s4_bank_pret SET 
            etudiant_id = ?, type_pret_id = ?, etablissement_id = ?, 
            montant_demande = ?, montant_accorde = ?, taux_applique = ?, 
            duree_mois = ?, mensualite = ?, montant_total = ?, statut = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data->etudiant_id,
            $data->type_pret_id,
            $data->etablissement_id,
            $data->montant_demande,
            $data->montant_accorde,
            $data->taux_applique,
            $data->duree_mois,
            $calculations['mensualite'],
            $calculations['montant_total'],
            $data->statut,
            $id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function deletePret($id) {
        // Vérifier si le prêt a des remboursements
        if ($this->hasPayments($id)) {
            throw new InvalidArgumentException("Impossible de supprimer un prêt ayant des remboursements");
        }

        $stmt = $this->db->prepare("DELETE FROM s4_bank_pret WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public function getRemboursements($pretId) {
        $stmt = $this->db->prepare("
            SELECT * FROM s4_bank_remboursement 
            WHERE pret_id = ? 
            ORDER BY numero_echeance
        ");
        $stmt->execute([$pretId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addRemboursement($pretId, $data) {
        // Vérifier si le prêt existe
        if (!$this->getPretById($pretId)) {
            throw new InvalidArgumentException("Prêt non trouvé");
        }

        $stmt = $this->db->prepare("
            INSERT INTO s4_bank_remboursement 
            (pret_id, numero_echeance, montant_prevu, date_echeance) 
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $pretId,
            $data->numero_echeance,
            $data->montant_prevu,
            $data->date_echeance
        ]);

        return $this->db->lastInsertId();
    }

    public function markPayment($remboursementId, $montantPaye) {
        $stmt = $this->db->prepare("
            UPDATE s4_bank_remboursement SET 
            montant_paye = ?, date_paiement = NOW(), statut = 'paye'
            WHERE id = ?
        ");

        $stmt->execute([$montantPaye, $remboursementId]);
        return $stmt->rowCount() > 0;
    }

    private function validatePretData($data) {
        if (empty($data->etudiant_id) || empty($data->type_pret_id) || empty($data->etablissement_id)) {
            throw new InvalidArgumentException("Les champs étudiant, type de prêt et établissement sont obligatoires");
        }

        if ($data->montant_accorde <= 0 || $data->duree_mois <= 0 || $data->taux_applique < 0) {
            throw new InvalidArgumentException("Les montants et durées doivent être positifs");
        }
    }

    private function calculateLoanPayments($montant, $taux, $duree) {
        $taux_mensuel = $taux / 100 / 12;

        if ($taux_mensuel == 0) {
            $mensualite = $montant / $duree;
        } else {
            $mensualite = ($montant * $taux_mensuel * pow(1 + $taux_mensuel, $duree)) /
                         (pow(1 + $taux_mensuel, $duree) - 1);
        }

        $montant_total = $mensualite * $duree;

        return [
            'mensualite' => round($mensualite, 2),
            'montant_total' => round($montant_total, 2)
        ];
    }

    private function checkFundsAvailability($etablissementId, $montant) {
        $stmt = $this->db->prepare("SELECT fonds_disponibles FROM s4_bank_etablissement WHERE id = ?");
        $stmt->execute([$etablissementId]);
        $fonds = $stmt->fetchColumn();

        return $fonds >= $montant;
    }

    private function hasPayments($pretId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM s4_bank_remboursement WHERE pret_id = ?");
        $stmt->execute([$pretId]);

        return $stmt->fetchColumn() > 0;
    }
}
