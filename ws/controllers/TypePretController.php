<?php

class TypePretController {

    public static function getAll() {
        try {
            $typesPrets = TypePretService::getAllTypesPrets();
            Flight::json($typesPrets);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des types de prêts: ' . $e->getMessage()], 500);
        }
    }

    public static function getById($id) {
        try {
            $typePret = TypePretService::getTypePretById($id);
            if ($typePret) {
                Flight::json($typePret);
            } else {
                Flight::json(['error' => 'Type de prêt non trouvé'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération du type de prêt: ' . $e->getMessage()], 500);
        }
    }

    public static function create() {
        try {
            $data = Flight::request()->data;

            // Validation des données
            $errors = TypePretService::validateTypePretData($data);
            if (!empty($errors)) {
                Flight::json(['error' => 'Données invalides', 'details' => $errors], 400);
                return;
            }

            $id = TypePretService::createTypePret($data);
            Flight::json(['message' => 'Type de prêt créé', 'id' => $id], 201);
        } catch (PDOException $e) {
            Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 400);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur interne: ' . $e->getMessage()], 500);
        }
    }

    public static function update($id) {
        try {
            $data = Flight::request()->data;

            // Validation des données
            $errors = TypePretService::validateTypePretData($data);
            if (!empty($errors)) {
                Flight::json(['error' => 'Données invalides', 'details' => $errors], 400);
                return;
            }

            $success = TypePretService::updateTypePret($id, $data);
            if ($success) {
                Flight::json(['message' => 'Type de prêt modifié']);
            } else {
                Flight::json(['error' => 'Échec de la modification'], 400);
            }
        } catch (PDOException $e) {
            Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 400);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur interne: ' . $e->getMessage()], 500);
        }
    }

    public static function delete($id) {
        try {
            $success = TypePretService::deleteTypePret($id);
            if ($success) {
                Flight::json(['message' => 'Type de prêt supprimé']);
            } else {
                Flight::json(['error' => 'Échec de la suppression'], 400);
            }
        } catch (PDOException $e) {
            Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 400);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur interne: ' . $e->getMessage()], 500);
        }
    }
}
