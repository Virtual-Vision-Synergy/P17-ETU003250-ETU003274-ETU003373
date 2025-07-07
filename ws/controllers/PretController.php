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
}
