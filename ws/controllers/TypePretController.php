<?php

require_once 'services/TypePretService.php';

class TypePretController {
    private $typePretService;

    public function __construct() {
        $this->typePretService = new TypePretService(getDB());
    }

    public function index() {
        try {
            $typesPrets = $this->typePretService->getAllTypesPrets();
            $this->jsonResponse($typesPrets);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id) {
        try {
            $typePret = $this->typePretService->getTypePretById($id);

            if ($typePret) {
                $this->jsonResponse($typePret);
            } else {
                $this->errorResponse('Type de prêt non trouvé', 404);
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = Flight::request()->data;
            $id = $this->typePretService->createTypePret($data);
            $this->successResponse('Type de prêt créé avec succès', ['id' => $id]);
        } catch (InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la création du type de prêt: ' . $e->getMessage(), 500);
        }
    }

    public function update($id) {
        try {
            $data = Flight::request()->data;
            $success = $this->typePretService->updateTypePret($id, $data);

            if ($success) {
                $this->successResponse('Type de prêt modifié avec succès');
            } else {
                $this->errorResponse('Aucune modification effectuée', 400);
            }
        } catch (InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la modification du type de prêt: ' . $e->getMessage(), 500);
        }
    }

    public function delete($id) {
        try {
            $success = $this->typePretService->deleteTypePret($id);

            if ($success) {
                $this->successResponse('Type de prêt supprimé avec succès');
            } else {
                $this->errorResponse('Type de prêt non trouvé', 404);
            }
        } catch (InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la suppression du type de prêt: ' . $e->getMessage(), 500);
        }
    }

    public function deactivate($id) {
        try {
            $success = $this->typePretService->deactivateTypePret($id);

            if ($success) {
                $this->successResponse('Type de prêt désactivé avec succès');
            } else {
                $this->errorResponse('Type de prêt non trouvé', 404);
            }
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la désactivation du type de prêt: ' . $e->getMessage(), 500);
        }
    }

    public function validateLoan() {
        try {
            $data = Flight::request()->data;
            $typePretId = $data->type_pret_id;
            $montant = $data->montant;
            $duree = $data->duree_mois;

            $this->typePretService->validateLoanAmount($typePretId, $montant);
            $this->typePretService->validateLoanDuration($typePretId, $duree);

            $this->successResponse('Validation réussie');
        } catch (InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la validation: ' . $e->getMessage(), 500);
        }
    }

    // Méthodes pour les vues (frontend)
    public function listView() {
        try {
            $typesPrets = $this->typePretService->getAllTypesPrets();
            $this->renderView('types-prets/list', ['typesPrets' => $typesPrets]);
        } catch (Exception $e) {
            $this->renderError('Erreur lors du chargement des types de prêts');
        }
    }

    public function createView() {
        $this->renderView('types-prets/create');
    }

    public function editView($id) {
        try {
            $typePret = $this->typePretService->getTypePretById($id);
            if ($typePret) {
                $this->renderView('types-prets/edit', ['typePret' => $typePret]);
            } else {
                $this->renderError('Type de prêt non trouvé');
            }
        } catch (Exception $e) {
            $this->renderError('Erreur lors du chargement du type de prêt');
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
        Flight::json(['view' => $view, 'data' => $data]);
    }

    private function renderError($message) {
        Flight::json(['error' => $message], 500);
    }
}
