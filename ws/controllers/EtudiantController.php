<?php

require_once 'services/EtudiantService.php';

class EtudiantController {
    private $etudiantService;

    public function __construct() {
        $this->etudiantService = new EtudiantService(getDB());
    }

    public function index() {
        try {
            $etudiants = $this->etudiantService->getAllEtudiants();
            $this->jsonResponse($etudiants);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id) {
        try {
            $etudiant = $this->etudiantService->getEtudiantById($id);

            if ($etudiant) {
                $this->jsonResponse($etudiant);
            } else {
                $this->errorResponse('Étudiant non trouvé', 404);
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = Flight::request()->data;
            $id = $this->etudiantService->createEtudiant($data);
            $this->successResponse('Étudiant ajouté avec succès', ['id' => $id]);
        } catch (InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la création de l\'étudiant', 500);
        }
    }

    public function update($id) {
        try {
            $data = Flight::request()->data;
            $success = $this->etudiantService->updateEtudiant($id, $data);

            if ($success) {
                $this->successResponse('Étudiant modifié avec succès');
            } else {
                $this->errorResponse('Aucune modification effectuée', 400);
            }
        } catch (InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la modification de l\'étudiant', 500);
        }
    }

    public function delete($id) {
        try {
            $success = $this->etudiantService->deleteEtudiant($id);

            if ($success) {
                $this->successResponse('Étudiant supprimé avec succès');
            } else {
                $this->errorResponse('Étudiant non trouvé', 404);
            }
        } catch (InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la suppression de l\'étudiant', 500);
        }
    }

    // Méthodes pour les vues (frontend)
    public function listView() {
        try {
            $etudiants = $this->etudiantService->getAllEtudiants();
            $this->renderView('etudiants/list', ['etudiants' => $etudiants]);
        } catch (Exception $e) {
            $this->renderError('Erreur lors du chargement des étudiants');
        }
    }

    public function createView() {
        $this->renderView('etudiants/create');
    }

    public function editView($id) {
        try {
            $etudiant = $this->etudiantService->getEtudiantById($id);
            if ($etudiant) {
                $this->renderView('etudiants/edit', ['etudiant' => $etudiant]);
            } else {
                $this->renderError('Étudiant non trouvé');
            }
        } catch (Exception $e) {
            $this->renderError('Erreur lors du chargement de l\'étudiant');
        }
    }

    // Méthodes utilitaires
    private function jsonResponse($data, $status = 200) {
        Flight::json($data, $status);
    }

    private function successResponse($message, $data = null) {
        $response = ['message' => $message];
        if ($data) {
            $response = array_merge($response, $data);
        }
        Flight::json($response);
    }

    private function errorResponse($message, $status = 400) {
        Flight::json(['error' => $message], $status);
    }

    private function renderView($view, $data = []) {
        // Pour l'instant, renvoie du JSON, mais peut être étendu pour du HTML
        Flight::json(['view' => $view, 'data' => $data]);
    }

    private function renderError($message) {
        Flight::json(['error' => $message], 500);
    }
}
