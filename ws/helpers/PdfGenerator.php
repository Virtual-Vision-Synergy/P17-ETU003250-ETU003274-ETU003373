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
        $this->SetFillColor(230, 230, 230);
        $this->Cell(0, 8, $this->convertText('INFORMATIONS DE L\'EMPRUNTEUR'), 1, 1, 'L', true);

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->convertText('Nom complet: ' . $this->pretData['etudiant_nom'] . ' ' . $this->pretData['etudiant_prenom']), 0, 1);
        $this->Cell(0, 6, $this->convertText('Email: ' . $this->pretData['etudiant_email']), 0, 1);
        $this->Ln(5);

        // Informations de l'établissement financier
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, $this->convertText('ÉTABLISSEMENT FINANCIER'), 1, 1, 'L', true);

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->convertText('Nom: ' . $this->pretData['etablissement_nom']), 0, 1);
        $this->Ln(5);

        // Type de prêt et caractéristiques
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, $this->convertText('TYPE DE PRÊT ET CARACTÉRISTIQUES'), 1, 1, 'L', true);

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->convertText('Type de prêt: ' . $this->pretData['type_pret_nom']), 0, 1);

        if (isset($this->pretData['type_description']) && !empty($this->pretData['type_description'])) {
            $this->Cell(0, 6, $this->convertText('Description: ' . $this->pretData['type_description']), 0, 1);
        }

        $this->Cell(0, 6, $this->convertText('Taux d\'intérêt annuel: ' . $this->pretData['type_taux'] . '%'), 0, 1);
        $this->Cell(0, 6, $this->convertText('Durée demandée: ' . $this->pretData['duree_mois'] . ' mois (' . round($this->pretData['duree_mois']/12, 1) . ' années)'), 0, 1);

        // Assurance
        if (isset($this->pretData['assurance_pourcentage']) && $this->pretData['assurance_pourcentage'] > 0) {
            $assurance_mensuelle = $this->pretData['montant_accorde'] * ($this->pretData['assurance_pourcentage'] / 100 / 12);
            $assurance_totale = $assurance_mensuelle * $this->pretData['duree_mois'];
            $this->Cell(0, 6, $this->convertText('Assurance: ' . $this->pretData['assurance_pourcentage'] . '% annuel'), 0, 1);
            $this->Cell(0, 6, $this->convertText('Assurance mensuelle: ' . number_format($assurance_mensuelle, 2, ',', ' ') . ' €'), 0, 1);
            $this->Cell(0, 6, $this->convertText('Assurance totale: ' . number_format($assurance_totale, 2, ',', ' ') . ' €'), 0, 1);
        } else {
            $this->Cell(0, 6, $this->convertText('Assurance: Aucune assurance souscrite'), 0, 1);
        }

        // But du prêt si disponible
        if (isset($this->pretData['but_pret']) && !empty($this->pretData['but_pret'])) {
            $this->Cell(0, 6, $this->convertText('Objet du prêt: ' . $this->pretData['but_pret']), 0, 1);
        }
        $this->Ln(5);

        // Montants et conditions financières
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, $this->convertText('CONDITIONS FINANCIÈRES'), 1, 1, 'L', true);

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->convertText('Montant demandé: ' . number_format($this->pretData['montant_demande'], 2, ',', ' ') . ' €'), 0, 1);

        if ($this->pretData['statut'] == 'approuve') {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 6, $this->convertText('Montant accordé: ' . number_format($this->pretData['montant_accorde'], 2, ',', ' ') . ' €'), 0, 1);
            $this->SetFont('Arial', '', 10);

            $this->Cell(0, 6, $this->convertText('Mensualité: ' . number_format($this->pretData['mensualite'], 2, ',', ' ') . ' €'), 0, 1);
            $this->Cell(0, 6, $this->convertText('Montant total à rembourser: ' . number_format($this->pretData['montant_total'], 2, ',', ' ') . ' €'), 0, 1);

            // Calculer le coût total du crédit
            $cout_credit = $this->pretData['montant_total'] - $this->pretData['montant_accorde'];
            $this->Cell(0, 6, $this->convertText('Coût total du crédit: ' . number_format($cout_credit, 2, ',', ' ') . ' €'), 0, 1);

            // Calculer le TEG (Taux Effectif Global) approximatif
            $teg = ($cout_credit / $this->pretData['montant_accorde']) * (12 / $this->pretData['duree_mois']) * 100;
            $this->Cell(0, 6, $this->convertText('Taux Effectif Global (TEG): ' . number_format($teg, 2) . '%'), 0, 1);

        } else {
            $this->Cell(0, 6, $this->convertText('Montant accordé: En attente d\'approbation'), 0, 1);

            // Estimation basée sur le montant demandé
            $mensualite_estimee = $this->calculerMensualiteEstimee($this->pretData['montant_demande'], $this->pretData['type_taux'], $this->pretData['duree_mois']);
            $montant_total_estime = $mensualite_estimee * $this->pretData['duree_mois'];

            $this->SetFont('Arial', 'I', 9);
            $this->Cell(0, 6, $this->convertText('Estimations (sous réserve d\'approbation):'), 0, 1);
            $this->Cell(0, 6, $this->convertText('  • Mensualité estimée: ' . number_format($mensualite_estimee, 2, ',', ' ') . ' €'), 0, 1);
            $this->Cell(0, 6, $this->convertText('  • Montant total estimé: ' . number_format($montant_total_estime, 2, ',', ' ') . ' €'), 0, 1);
            $this->SetFont('Arial', '', 10);
        }
        $this->Ln(5);

        // Statut et dates
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, $this->convertText('STATUT ET CHRONOLOGIE'), 1, 1, 'L', true);

        $this->SetFont('Arial', '', 10);
        $statusText = strtoupper($this->pretData['statut']);
        if ($this->pretData['statut'] == 'en_attente') {
            $statusText = 'EN ATTENTE D\'EXAMEN';
        } elseif ($this->pretData['statut'] == 'approuve') {
            $statusText = 'APPROUVÉ';
        } elseif ($this->pretData['statut'] == 'refuse') {
            $statusText = 'REFUSÉ';
        }

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, $this->convertText('Statut actuel: ' . $statusText), 0, 1);
        $this->SetFont('Arial', '', 10);

        $this->Cell(0, 6, $this->convertText('Date de demande: ' . date('d/m/Y à H:i', strtotime($this->pretData['date_demande']))), 0, 1);

        if (!empty($this->pretData['date_approbation'])) {
            $this->Cell(0, 6, $this->convertText('Date d\'approbation: ' . date('d/m/Y à H:i', strtotime($this->pretData['date_approbation']))), 0, 1);
        }

        if (!empty($this->pretData['date_debut'])) {
            $this->Cell(0, 6, $this->convertText('Date de début de remboursement: ' . date('d/m/Y', strtotime($this->pretData['date_debut']))), 0, 1);
        }

        if (!empty($this->pretData['date_fin_prevue'])) {
            $this->Cell(0, 6, $this->convertText('Date de fin prévue: ' . date('d/m/Y', strtotime($this->pretData['date_fin_prevue']))), 0, 1);
        }
        $this->Ln(10);

        // Conditions générales (pour tous les statuts)
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, $this->convertText('CONDITIONS GÉNÉRALES'), 1, 1, 'L', true);
        $this->SetFont('Arial', '', 9);

        if ($this->pretData['statut'] == 'approuve') {
            $conditions = [
                '1. Le présent contrat de prêt est consenti par l\'établissement financier mentionné ci-dessus.',
                '2. L\'emprunteur s\'engage à rembourser le montant emprunté selon l\'échéancier convenu.',
                '3. Le taux d\'intérêt applicable est celui mentionné dans les détails du prêt.',
                '4. Tout retard de paiement pourra donner lieu à des pénalités de retard.',
                '5. L\'emprunteur peut procéder à un remboursement anticipé sans pénalités.',
                '6. Ce contrat est soumis au droit français et aux réglementations bancaires en vigueur.',
                '7. En cas de litige, les parties s\'engagent à rechercher une solution amiable avant tout recours judiciaire.'
            ];
        } elseif ($this->pretData['statut'] == 'en_attente') {
            $conditions = [
                '1. Cette demande de prêt est en cours d\'examen par l\'établissement financier.',
                '2. L\'approbation de ce prêt est soumise à l\'étude du dossier et aux critères de l\'établissement.',
                '3. Les conditions mentionnées sont indicatives et peuvent être modifiées lors de l\'approbation.',
                '4. L\'établissement se réserve le droit d\'approuver, de modifier ou de refuser cette demande.',
                '5. En cas d\'approbation, un nouveau contrat sera établi avec les conditions définitives.'
            ];
        } else { // refuse
            $conditions = [
                '1. Cette demande de prêt a été examinée et n\'a pas pu être approuvée.',
                '2. Cette décision est basée sur les critères d\'évaluation de l\'établissement financier.',
                '3. L\'emprunteur peut, sous certaines conditions, reformuler une nouvelle demande.',
                '4. Ce document atteste de la demande effectuée et de la décision prise.'
            ];
        }

        foreach ($conditions as $condition) {
            $this->Cell(0, 5, $this->convertText($condition), 0, 1);
            $this->Ln(1);
        }

        $this->Ln(10);

        // Signatures (seulement pour les prêts approuvés)
        if ($this->pretData['statut'] == 'approuve') {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(95, 6, $this->convertText('Signature de l\'emprunteur:'), 0, 0);
            $this->Cell(95, 6, $this->convertText('Signature de l\'établissement:'), 0, 1);
            $this->Ln(20);
            $this->Cell(95, 6, 'Date: ____________________', 0, 0);
            $this->Cell(95, 6, 'Date: ____________________', 0, 1);
        }
    }

    // Fonction pour calculer une mensualité estimée
    private function calculerMensualiteEstimee($montant, $taux_annuel, $duree_mois)
    {
        $taux_mensuel = ($taux_annuel / 100) / 12;
        if ($taux_mensuel == 0) {
            return $montant / $duree_mois;
        }
        return $montant * ($taux_mensuel * pow(1 + $taux_mensuel, $duree_mois)) / (pow(1 + $taux_mensuel, $duree_mois) - 1);
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
