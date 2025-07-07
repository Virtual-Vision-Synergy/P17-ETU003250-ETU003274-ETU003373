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

    public static function approveOrReject($id) {
        try {
            $data = Flight::request()->data;

            if (!isset($data['approve'])) {
                throw new InvalidArgumentException('Le paramètre "approve" est requis');
            }

            $approve = (bool)$data['approve'];
            $montantAccorde = isset($data['montant_accorde']) ? (float)$data['montant_accorde'] : 0;
            $commentaire = isset($data['commentaire']) ? $data['commentaire'] : null;

            // Vérification des règles métier
            if ($approve && $montantAccorde <= 0) {
                throw new InvalidArgumentException('Le montant accordé doit être supérieur à zéro pour une approbation');
            }

            if (!$approve) {
                $montantAccorde = 0; // Forcer le montant à 0 en cas de refus
            }

            // Appel au service pour mettre à jour le prêt
            $result = PretService::updatePretStatus($id, $approve, $montantAccorde, $commentaire);

            Flight::json([
                'message' => $approve ? 'Prêt approuvé avec succès' : 'Prêt refusé',
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
