<?php

require_once '../fpdf/fpdf.php';

class PdfGenerator extends FPDF
{
    private $pretData;

    public function __construct($pretData)
    {
        parent::__construct();
        $this->pretData = $pretData;
    }

    // Fonction pour convertir les caractères UTF-8 vers ISO-8859-1
    private function convertText($text)
    {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
    }

    function Header()
    {
        // Logo ou titre
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, $this->convertText('CONTRAT DE PRÊT ÉTUDIANT'), 0, 1, 'C');
        $this->Ln(5);

        // Informations de l'établissement
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 8, $this->convertText('Établissement: ' . $this->pretData['etablissement_nom']), 0, 1);
        $this->Ln(5);

        // Ligne de séparation
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $this->convertText('Page ' . $this->PageNo() . ' - Document généré le ' . date('d/m/Y à H:i')), 0, 0, 'C');
    }

    public function generatePretContract()
    {
        $this->AddPage();

        // Titre du document
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, $this->convertText('CONTRAT DE PRÊT N°' . $this->pretData['id']), 0, 1, 'C');
        $this->Ln(10);

        // Informations de l'étudiant
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, $this->convertText('INFORMATIONS DE L\'EMPRUNTEUR'), 0, 1);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->convertText('Nom: ' . $this->pretData['etudiant_nom'] . ' ' . $this->pretData['etudiant_prenom']), 0, 1);
        $this->Cell(0, 6, $this->convertText('Email: ' . $this->pretData['etudiant_email']), 0, 1);
        $this->Ln(5);

        // Informations du prêt
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, $this->convertText('DÉTAILS DU PRÊT'), 0, 1);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->convertText('Type de prêt: ' . $this->pretData['type_pret_nom']), 0, 1);
        $this->Cell(0, 6, $this->convertText('Montant demandé: ' . number_format($this->pretData['montant_demande'], 2, ',', ' ') . ' €'), 0, 1);

        if ($this->pretData['statut'] == 'approuve') {
            $this->Cell(0, 6, $this->convertText('Montant accordé: ' . number_format($this->pretData['montant_accorde'], 2, ',', ' ') . ' €'), 0, 1);
            $this->Cell(0, 6, $this->convertText('Durée: ' . $this->pretData['duree_mois'] . ' mois'), 0, 1);
            $this->Cell(0, 6, $this->convertText('Taux d\'intérêt: ' . $this->pretData['type_taux'] . '%'), 0, 1);
            $this->Cell(0, 6, $this->convertText('Mensualité: ' . number_format($this->pretData['mensualite'], 2, ',', ' ') . ' €'), 0, 1);
            $this->Cell(0, 6, $this->convertText('Montant total à rembourser: ' . number_format($this->pretData['montant_total'], 2, ',', ' ') . ' €'), 0, 1);

            if (isset($this->pretData['assurance_pourcentage']) && $this->pretData['assurance_pourcentage'] > 0) {
                $this->Cell(0, 6, $this->convertText('Assurance: ' . $this->pretData['assurance_pourcentage'] . '%'), 0, 1);
            }
        }

        $this->Cell(0, 6, $this->convertText('Statut: ' . strtoupper($this->pretData['statut'])), 0, 1);
        $this->Cell(0, 6, $this->convertText('Date de demande: ' . date('d/m/Y', strtotime($this->pretData['date_demande']))), 0, 1);

        if (!empty($this->pretData['date_approbation'])) {
            $this->Cell(0, 6, $this->convertText('Date d\'approbation: ' . date('d/m/Y', strtotime($this->pretData['date_approbation']))), 0, 1);
        }

        if (!empty($this->pretData['date_debut'])) {
            $this->Cell(0, 6, $this->convertText('Date de début: ' . date('d/m/Y', strtotime($this->pretData['date_debut']))), 0, 1);
        }

        if (!empty($this->pretData['date_fin_prevue'])) {
            $this->Cell(0, 6, $this->convertText('Date de fin prévue: ' . date('d/m/Y', strtotime($this->pretData['date_fin_prevue']))), 0, 1);
        }

        $this->Ln(10);

        // Conditions générales
        if ($this->pretData['statut'] == 'approuve') {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 8, $this->convertText('CONDITIONS GÉNÉRALES'), 0, 1);
            $this->SetFont('Arial', '', 10);

            $conditions = [
                '1. Le présent contrat de prêt est consenti par l\'établissement financier mentionné ci-dessus.',
                '2. L\'emprunteur s\'engage à rembourser le montant emprunté selon l\'échéancier convenu.',
                '3. Le taux d\'intérêt applicable est celui mentionné dans les détails du prêt.',
                '4. Tout retard de paiement pourra donner lieu à des pénalités de retard.',
                '5. L\'emprunteur peut procéder à un remboursement anticipé sans pénalités.',
                '6. Ce contrat est soumis au droit français.'
            ];

            foreach ($conditions as $condition) {
                $this->Cell(0, 6, $this->convertText($condition), 0, 1);
                $this->Ln(2);
            }
        }

        $this->Ln(15);

        // Signatures
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(95, 6, $this->convertText('Signature de l\'emprunteur:'), 0, 0);
        $this->Cell(95, 6, $this->convertText('Signature de l\'établissement:'), 0, 1);
        $this->Ln(20);
        $this->Cell(95, 6, 'Date: ____________________', 0, 0);
        $this->Cell(95, 6, 'Date: ____________________', 0, 1);
    }

    public function generateTableauAmortissement()
    {
        if ($this->pretData['statut'] != 'approuve') {
            return; // Ne génère le tableau que pour les prêts approuvés
        }

        $this->AddPage();

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, $this->convertText('TABLEAU D\'AMORTISSEMENT'), 0, 1, 'C');
        $this->Ln(5);

        // En-têtes du tableau
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(20, 8, 'Mois', 1, 0, 'C');
        $this->Cell(25, 8, 'Capital', 1, 0, 'C');
        $this->Cell(25, 8, $this->convertText('Intérêts'), 1, 0, 'C');
        $this->Cell(25, 8, $this->convertText('Mensualité'), 1, 0, 'C');
        $this->Cell(30, 8, 'Capital restant', 1, 0, 'C');
        $this->Cell(25, 8, 'Date', 1, 1, 'C');

        $this->SetFont('Arial', '', 8);

        $montant = $this->pretData['montant_accorde'];
        $taux_mensuel = $this->pretData['type_taux'] / 100 / 12;
        $duree = $this->pretData['duree_mois'];
        $mensualite = $this->pretData['mensualite'];
        $capital_restant = $montant;

        $date_debut = new DateTime($this->pretData['date_debut'] ?? date('Y-m-d'));

        for ($mois = 1; $mois <= $duree; $mois++) {
            $interets = $capital_restant * $taux_mensuel;
            $capital = $mensualite - $interets;
            $capital_restant -= $capital;

            if ($capital_restant < 0) $capital_restant = 0;

            $date_echeance = clone $date_debut;
            $date_echeance->add(new DateInterval('P' . ($mois - 1) . 'M'));

            $this->Cell(20, 6, $mois, 1, 0, 'C');
            $this->Cell(25, 6, number_format($capital, 2, ',', ' '), 1, 0, 'R');
            $this->Cell(25, 6, number_format($interets, 2, ',', ' '), 1, 0, 'R');
            $this->Cell(25, 6, number_format($mensualite, 2, ',', ' '), 1, 0, 'R');
            $this->Cell(30, 6, number_format($capital_restant, 2, ',', ' '), 1, 0, 'R');
            $this->Cell(25, 6, $date_echeance->format('d/m/Y'), 1, 1, 'C');
        }
    }
}
