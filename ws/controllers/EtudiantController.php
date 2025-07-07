<?php

class EtudiantController {

    public static function getAll() {
        try {
            $etudiants = EtudiantService::getAllEtudiants();
            Flight::json($etudiants);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des étudiants: ' . $e->getMessage()], 500);
        }
    }

    public static function getById($id) {
        try {
            $etudiant = EtudiantService::getEtudiantById($id);
            if ($etudiant) {
                Flight::json($etudiant);
            } else {
                Flight::json(['error' => 'Étudiant non trouvé'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération de l\'étudiant: ' . $e->getMessage()], 500);
        }
    }

    public static function create() {
        try {
            $data = Flight::request()->data;

            // Validation des données
            $errors = EtudiantService::validateEtudiantData($data);
            if (!empty($errors)) {
                Flight::json(['error' => 'Données invalides', 'details' => $errors], 400);
                return;
            }

            $id = EtudiantService::createEtudiant($data);
            Flight::json(['message' => 'Étudiant ajouté', 'id' => $id], 201);
        } catch (PDOException $e) {
            Flight::json(['error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()], 400);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur interne: ' . $e->getMessage()], 500);
        }
    }

    public static function update($id) {
        try {
            $data = Flight::request()->data;

            // Validation des données
            $errors = EtudiantService::validateEtudiantData($data);
            if (!empty($errors)) {
                Flight::json(['error' => 'Données invalides', 'details' => $errors], 400);
                return;
            }

            $success = EtudiantService::updateEtudiant($id, $data);
            if ($success) {
                Flight::json(['message' => 'Étudiant modifié']);
            } else {
                Flight::json(['error' => 'Échec de la modification'], 400);
            }
        } catch (PDOException $e) {
            Flight::json(['error' => 'Erreur lors de la modification: ' . $e->getMessage()], 400);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur interne: ' . $e->getMessage()], 500);
        }
    }

    public static function delete($id) {
        try {
            $success = EtudiantService::deleteEtudiant($id);
            if ($success) {
                Flight::json(['message' => 'Étudiant supprimé']);
            } else {
                Flight::json(['error' => 'Échec de la suppression'], 400);
            }
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()], 500);
        }
    }
}
