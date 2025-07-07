<?php

class EtablissementController {

    public static function getAll() {
        try {
            $etablissements = EtablissementService::getAllEtablissements();
            Flight::json($etablissements);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des établissements: ' . $e->getMessage()], 500);
        }
    }

    public static function getById($id) {
        try {
            $etablissement = EtablissementService::getEtablissementById($id);
            if ($etablissement) {
                Flight::json($etablissement);
            } else {
                Flight::json(['error' => 'Établissement non trouvé'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération de l\'établissement: ' . $e->getMessage()], 500);
        }
    }

    public static function create() {
        try {
            $data = Flight::request()->data;

            // Validation des données
            $errors = EtablissementService::validateEtablissementData($data);
            if (!empty($errors)) {
                Flight::json(['error' => 'Données invalides', 'details' => $errors], 400);
                return;
            }

            $id = EtablissementService::createEtablissement($data);
            Flight::json(['message' => 'Établissement créé', 'id' => $id], 201);
        } catch (PDOException $e) {
            Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 400);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur interne: ' . $e->getMessage()], 500);
        }
    }

    public static function depot($id) {
        try {
            $data = Flight::request()->data;
            $montant = $data->montant;
            $description = $data->description ?? null;

            $result = EtablissementService::depot($id, $montant, $description);
            Flight::json([
                'message' => 'Fonds ajoutés',
                'ancien_solde' => $result['ancien_solde'],
                'nouveau_solde' => $result['nouveau_solde']
            ]);
        } catch (InvalidArgumentException $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
}
