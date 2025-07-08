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
        // Vérifier que le texte n'est pas vide
        if (empty($text)) {
            return '';
        }

        // Nettoyer les caractères de contrôle non imprimables
        $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);

        // Essayer d'abord la conversion avec TRANSLIT
        $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);

        // Si la conversion échoue, essayer sans TRANSLIT
        if ($converted === false) {
            $converted = @iconv('UTF-8', 'ISO-8859-1//IGNORE', $text);
        }

        // Si ça échoue encore, utiliser une approche de fallback
        if ($converted === false) {
            // Remplacer les caractères accentués manuellement
            $accents = [
                'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
                'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
                'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
                'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
                'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
                'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
                'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
                'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
                'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
                'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
                'Ç' => 'C', 'ç' => 'c',
                'Ñ' => 'N', 'ñ' => 'n',
                'Ý' => 'Y', 'ý' => 'y', 'ÿ' => 'y',
                '€' => 'EUR', '£' => 'GBP', '¥' => 'JPY'
            ];
            $converted = strtr($text, $accents);

            // Supprimer les caractères non-ASCII restants
            $converted = preg_replace('/[^\x20-\x7E]/', '', $converted);
        }

        return $converted;
    }

    function Header()
    {
        // Logo ou titre
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, $this->convertText('CONTRAT DE PRÊT ÉTUDIANT'), 0, 1, 'C');
        $this->Ln(3);

        // Informations de l'établissement
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->convertText('Établissement: ' . $this->pretData['etablissement_nom']), 0, 1);
        $this->Ln(2);

        // Ligne de séparation
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
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
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, $this->convertText('CONTRAT DE PRÊT N°' . $this->pretData['id']), 0, 1, 'C');
        $this->Ln(5);

        // Section principale en deux colonnes
        $col_width = 95;
        $start_y = $this->GetY();

        // Colonne gauche - Informations personnelles
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(230, 230, 230);
        $this->Cell($col_width, 6, $this->convertText('EMPRUNTEUR'), 1, 0, 'L', true);

        // Colonne droite - Établissement
        $this->Cell($col_width, 6, $this->convertText('ÉTABLISSEMENT FINANCIER'), 1, 1, 'L', true);

        $this->SetFont('Arial', '', 8);

        // Informations emprunteur (colonne gauche)
        $this->Cell($col_width, 5, $this->convertText('Nom: ' . $this->pretData['etudiant_nom'] . ' ' . $this->pretData['etudiant_prenom']), 'LR', 0);
        // Informations établissement (colonne droite)
        $this->Cell($col_width, 5, $this->convertText('Nom: ' . $this->pretData['etablissement_nom']), 'LR', 1);

        $this->Cell($col_width, 5, $this->convertText('Email: ' . $this->pretData['etudiant_email']), 'LRB', 0);
        $this->Cell($col_width, 5, '', 'LRB', 1);

        $this->Ln(3);

        // Section caractéristiques du prêt
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, $this->convertText('CARACTÉRISTIQUES DU PRÊT'), 1, 1, 'L', true);

        $this->SetFont('Arial', '', 8);

        // Première ligne de caractéristiques
        $this->Cell($col_width, 5, $this->convertText('Type: ' . $this->pretData['type_pret_nom']), 'LR', 0);
        $this->Cell($col_width, 5, $this->convertText('Taux d\'intérêt: ' . $this->pretData['type_taux'] . '% annuel'), 'LR', 1);

        // Deuxième ligne avec durée et délai
        $duree_annees = round($this->pretData['duree_mois']/12, 1);
        $this->Cell($col_width, 5, $this->convertText('Durée: ' . $this->pretData['duree_mois'] . ' mois (' . $duree_annees . ' ans)'), 'LR', 0);

        // Ajout du délai de grâce
        $delai_grace = isset($this->pretData['delai']) ? $this->pretData['delai'] : 0;
        $this->Cell($col_width, 5, $this->convertText('Délai de grâce: ' . $delai_grace . ' mois'), 'LR', 1);

        // Troisième ligne avec montants
        $this->Cell($col_width, 5, $this->convertText('Montant demandé: ' . number_format($this->pretData['montant_demande'], 0, ',', ' ') . ' €'), 'LR', 0);

        if ($this->pretData['statut'] == 'approuve') {
            $this->Cell($col_width, 5, $this->convertText('Montant accordé: ' . number_format($this->pretData['montant_accorde'], 0, ',', ' ') . ' €'), 'LR', 1);
        } else {
            $this->Cell($col_width, 5, $this->convertText('Statut: ' . $this->getStatusText()), 'LR', 1);
        }

        // Assurance et objet du prêt
        if (isset($this->pretData['assurance_pourcentage']) && $this->pretData['assurance_pourcentage'] > 0) {
            $this->Cell($col_width, 5, $this->convertText('Assurance: ' . $this->pretData['assurance_pourcentage'] . '% annuel'), 'LR', 0);
        } else {
            $this->Cell($col_width, 5, $this->convertText('Assurance: Aucune'), 'LR', 0);
        }

        if (isset($this->pretData['but_pret']) && !empty($this->pretData['but_pret'])) {
            $but_court = substr($this->pretData['but_pret'], 0, 30) . (strlen($this->pretData['but_pret']) > 30 ? '...' : '');
            $this->Cell($col_width, 5, $this->convertText('Objet: ' . $but_court), 'LR', 1);
        } else {
            $this->Cell($col_width, 5, '', 'LR', 1);
        }

        // Fermeture du tableau
        $this->Cell(0, 0, '', 'T', 1);
        $this->Ln(3);

        // Section conditions financières compacte
        if ($this->pretData['statut'] == 'approuve') {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 6, $this->convertText('CONDITIONS FINANCIÈRES'), 1, 1, 'L', true);

            $this->SetFont('Arial', '', 8);
            $this->Cell($col_width, 5, $this->convertText('Mensualité: ' . number_format($this->pretData['mensualite'], 2, ',', ' ') . ' €'), 'LR', 0);
            $this->Cell($col_width, 5, $this->convertText('Total à rembourser: ' . number_format($this->pretData['montant_total'], 2, ',', ' ') . ' €'), 'LR', 1);

            $cout_credit = $this->pretData['montant_total'] - $this->pretData['montant_accorde'];
            $teg = ($cout_credit / $this->pretData['montant_accorde']) * (12 / $this->pretData['duree_mois']) * 100;

            $this->Cell($col_width, 5, $this->convertText('Coût du crédit: ' . number_format($cout_credit, 2, ',', ' ') . ' €'), 'LRB', 0);
            $this->Cell($col_width, 5, $this->convertText('TEG: ' . number_format($teg, 2) . '%'), 'LRB', 1);

        } else {
            // Estimations pour les demandes en attente
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 6, $this->convertText('ESTIMATIONS'), 1, 1, 'L', true);

            $mensualite_estimee = $this->calculerMensualiteEstimee($this->pretData['montant_demande'], $this->pretData['type_taux'], $this->pretData['duree_mois']);
            $montant_total_estime = $mensualite_estimee * $this->pretData['duree_mois'];

            $this->SetFont('Arial', 'I', 8);
            $this->Cell($col_width, 5, $this->convertText('Mensualité estimée: ' . number_format($mensualite_estimee, 2, ',', ' ') . ' €'), 'LR', 0);
            $this->Cell($col_width, 5, $this->convertText('Total estimé: ' . number_format($montant_total_estime, 2, ',', ' ') . ' €'), 'LR', 1);
            $this->Cell(0, 0, '', 'T', 1);
        }

        $this->Ln(3);

        // Chronologie compacte
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, $this->convertText('CHRONOLOGIE'), 1, 1, 'L', true);

        $this->SetFont('Arial', '', 8);
        $this->Cell($col_width, 5, $this->convertText('Demande: ' . date('d/m/Y', strtotime($this->pretData['date_demande']))), 'LR', 0);

        if (!empty($this->pretData['date_approbation'])) {
            $this->Cell($col_width, 5, $this->convertText('Approbation: ' . date('d/m/Y', strtotime($this->pretData['date_approbation']))), 'LR', 1);
        } else {
            $this->Cell($col_width, 5, $this->convertText('Statut: ' . $this->getStatusText()), 'LR', 1);
        }

        // Calcul correct avec date_debut + délai
        if (!empty($this->pretData['date_debut'])) {
            $delai_grace = isset($this->pretData['delai']) ? $this->pretData['delai'] : 0;
            $date_debut = new DateTime($this->pretData['date_debut']);
            $date_debut_ajuste = clone $date_debut;
            $date_debut_ajuste->add(new DateInterval('P' . $delai_grace . 'M'));
            $this->Cell($col_width, 5, $this->convertText('Début remboursement: ' . $date_debut_ajuste->format('d/m/Y')), 'LR', 0);
        } else {
            $this->Cell($col_width, 5, '', 'LR', 0);
        }

        // Calcul de la date de fin basé sur le début de remboursement + durée (forcé pour corriger les erreurs)
        if (!empty($this->pretData['date_debut'])) {
            $delai_grace = isset($this->pretData['delai']) ? $this->pretData['delai'] : 0;
            $date_debut = new DateTime($this->pretData['date_debut']);
            // Calculer la date de début de remboursement
            $date_debut_remboursement = clone $date_debut;
            $date_debut_remboursement->add(new DateInterval('P' . $delai_grace . 'M'));
            // Ajouter la durée du prêt à partir du début de remboursement
            $date_fin_calcule = clone $date_debut_remboursement;
            $date_fin_calcule->add(new DateInterval('P' . $this->pretData['duree_mois'] . 'M'));
            $this->Cell($col_width, 5, $this->convertText('Fin prévue: ' . $date_fin_calcule->format('d/m/Y')), 'LR', 1);
        } else {
            $this->Cell($col_width, 5, '', 'LR', 1);
        }

        $this->Cell(0, 0, '', 'T', 1);
        $this->Ln(5);

        // Conditions générales condensées
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->convertText('CONDITIONS GÉNÉRALES'), 1, 1, 'L', true);
        $this->SetFont('Arial', '', 7);

        $conditions = $this->getConditionsCompactes();
        foreach ($conditions as $condition) {
            $this->Cell(0, 3, $this->convertText($condition), 0, 1);
        }

        $this->Ln(5);

        // Signatures pour les prêts approuvés
        if ($this->pretData['statut'] == 'actif') {
            $this->SetFont('Arial', 'B', 9);
            $this->Cell($col_width, 5, $this->convertText('Signature emprunteur:'), 'LTR', 0);
            $this->Cell($col_width, 5, $this->convertText('Signature établissement:'), 'LTR', 1);
            $this->Cell($col_width, 15, '', 'LR', 0);
            $this->Cell($col_width, 15, '', 'LR', 1);
            $this->Cell($col_width, 5, 'Date: _______________', 'LBR', 0);
            $this->Cell($col_width, 5, 'Date: _______________', 'LBR', 1);
        }
    }

    private function getStatusText()
    {
        switch ($this->pretData['statut']) {
            case 'en_attente': return 'EN ATTENTE';
            case 'approuve': return 'APPROUVÉ';
            case 'refuse': return 'REFUSÉ';
            default: return strtoupper($this->pretData['statut']);
        }
    }

    private function getConditionsCompactes()
    {
        // Vérification du statut pour les prêts approuvés/actifs
        if ($this->pretData['statut'] == 'actif' || $this->pretData['statut'] == 'approuve') {
            return [
                '• Remboursement selon échéancier convenu • Taux d\'intérêt fixe mentionné ci-dessus',
                '• Remboursement anticipé autorisé sans pénalités • Pénalités en cas de retard de paiement',
                '• Contrat soumis au droit français • Résolution amiable privilégiée en cas de litige'
            ];
        } elseif ($this->pretData['statut'] == 'en_attente') {
            return [
                '• Demande en cours d\'examen • Conditions indicatives susceptibles de modification',
                '• Approbation soumise aux critères de l\'établissement'
            ];
        } elseif ($this->pretData['statut'] == 'refuse') {
            return [
                '• Demande examinée et non approuvée • Possibilité de nouvelle demande sous conditions'
            ];
        } else {
            // Pour tout autre statut, afficher le statut actuel
            return [
                '• Statut actuel: ' . strtoupper($this->pretData['statut']) . ' • Veuillez contacter votre conseiller pour plus d\'informations'
            ];
        }
    }

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
