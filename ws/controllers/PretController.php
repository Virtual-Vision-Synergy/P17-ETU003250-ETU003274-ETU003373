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

            $pret = PretService::getPretById($id);

            $montant_accorde = $data->montant_accorde ?? 0.0;
            $mensualite = PretService::calculMensualite($montant_accorde, $pret['type_taux'], $pret['duree_mois']);
            $montant_total = $montant_accorde + ($montant_accorde * ($pret['type_taux'] /100));
            $statut = $data->statut ?? 'en attente';
            $date_approbation = date('Y-m-d H:i:s');
            $date_debut = $data->date_debut ?? date('Y-m-d H:i:s');
            $date_fin_prevue = date('Y-m-d H:i:s', strtotime("+{$pret['duree_mois']} months"));
            $assurance_pourcentage = $data->assurance_pourcentage ?? 0.00;

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
                'etablissement_id' => $pret['etablissement_id'],
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
}
