<?php

class PretController {

    public static function getAll() {
        try {
            $prets = PretService::getAllPrets();
            Flight::json($prets);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des prêts: ' . $e->getMessage()], 500);
        }
    }

    public static function getAllInProcess()
    {
        try {
            $prets = PretService::getAllPretsInProcess();
            Flight::json($prets);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des prêts: ' . $e->getMessage()], 500);
        }
    }

    public static function getById($id) {
        try {
            $pret = PretService::getPretById($id);
            if ($pret) {
                Flight::json($pret);
            } else {
                Flight::json(['error' => 'Prêt non trouvé'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération du prêt: ' . $e->getMessage()], 500);
        }
    }

    public static function create() {
        try {
            $data = Flight::request()->data;
            $id = PretService::createPret($data);
            Flight::json(['message' => 'Prêt créé', 'id' => $id], 201);
        } catch (InvalidArgumentException $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    public static function approve($id) {
        try {
            $data = Flight::request()->data;

            $pret = Pret::getPretById($id);

            $montant_accorde = $data->montant_accorde ?? 0.0;
            // Utiliser l'assurance déjà définie lors de la création du prêt
            $assurance_pourcentage = $pret['assurance_pourcentage'] ?? 0.0;

            // Calculer le montant d'assurance
            $montant_assurance = Pret::calculMontantAssurance($montant_accorde, $assurance_pourcentage);

            // Calculer la mensualité sur le montant accordé
            $mensualite = Pret::calculMensualite($montant_accorde, $pret['type_taux'], $pret['duree_mois']);

            // Calculer le montant total (capital + intérêts + assurance)
            $montant_total_interets = $mensualite * $pret['duree_mois'];
            $montant_total = $montant_total_interets + $montant_assurance;

            $statut = $data->statut ?? 'en_attente';
            $date_approbation = date('Y-m-d H:i:s');
            $date_debut = $data->date_debut ?? date('Y-m-d H:i:s');
            $date_fin_prevue = date('Y-m-d H:i:s', strtotime("+{$pret['duree_mois']} months", strtotime($date_debut)));

            $n_data = [
                'montant_accorde' => $montant_accorde,
                'mensualite' => $mensualite,
                'montant_total' => $montant_total,
                'assurance_pourcentage' => $assurance_pourcentage,
                'statut' => $statut,
                'date_approbation' => $date_approbation,
                'date_debut' => $date_debut,
                'date_fin_prevue' => $date_fin_prevue,
                'duree_mois' => $pret['duree_mois'],
                'etablissement_id' => $pret['etablissement_id']
            ];

            $result = PretService::approvePret($id, $n_data);

            Flight::json([
                'message' => $statut != "refuse" ? 'Prêt approuvé avec succès' : 'Prêt refusé',
                'id' => $id,
                'success' => true
            ]);

        } catch (InvalidArgumentException $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors du traitement de la demande: ' . $e->getMessage()], 500);
        }
    }

    public static function generatePdf($id) {
        try {
            // Récupérer les données du prêt
            $pret = PretService::getPretById($id);
            if (!$pret) {
                Flight::json(['error' => 'Prêt non trouvé'], 404);
                return;
            }

            // Inclure la classe PdfGenerator
            require_once '../ws/helpers/PdfGenerator.php';

            // Créer une instance du générateur PDF
            $pdfGenerator = new PdfGenerator($pret);

            // Générer le contrat
            $pdfGenerator->generatePretContract();

            // Générer le tableau d'amortissement si le prêt est approuvé
            if ($pret['statut'] == 'approuve') {
                $pdfGenerator->generateTableauAmortissement();
            }

            // Définir les en-têtes pour le téléchargement
            $filename = 'contrat_pret_' . $pret['id'] . '_' . date('Y-m-d') . '.pdf';

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Sortir le PDF
            $pdfGenerator->Output('D', $filename);

        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()], 500);
        }
    }

    public static function generatePdfInline($id) {
        try {
            // Récupérer les données du prêt
            $pret = PretService::getPretById($id);
            if (!$pret) {
                Flight::json(['error' => 'Prêt non trouvé'], 404);
                return;
            }

            // Inclure la classe PdfGenerator
            require_once '../ws/helpers/PdfGenerator.php';

            // Créer une instance du générateur PDF
            $pdfGenerator = new PdfGenerator($pret);

            // Générer le contrat
            $pdfGenerator->generatePretContract();

            // Générer le tableau d'amortissement si le prêt est approuvé
            if ($pret['statut'] == 'approuve') {
                $pdfGenerator->generateTableauAmortissement();
            }

            // Définir les en-têtes pour l'affichage inline
            $filename = 'contrat_pret_' . $pret['id'] . '_' . date('Y-m-d') . '.pdf';

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Afficher le PDF dans le navigateur
            $pdfGenerator->Output('I', $filename);

        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()], 500);
        }
    }
}
