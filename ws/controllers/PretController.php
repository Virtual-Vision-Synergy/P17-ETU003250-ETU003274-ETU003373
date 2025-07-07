<?php

require_once 'services/PretService.php';

class PretController {
    private $pretService;

    public function __construct() {
        $this->pretService = new PretService(getDB());
    }

    public function index() {
        try {
            $prets = $this->pretService->getAllPrets();
            $this->jsonResponse($prets);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id) {
        try {
            $pret = $this->pretService->getPretById($id);

            if ($pret) {
                $this->jsonResponse($pret);
            } else {
                $this->errorResponse('Prêt non trouvé', 404);
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = Flight::request()->data;
            $id = $this->pretService->createPret($data);
            $this->successResponse('Prêt créé avec succès', ['id' => $id]);
        } catch (InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la création du prêt: ' . $e->getMessage(), 500);
        }
    }

    public function update($id) {
        try {
            $data = Flight::request()->data;
            $success = $this->pretService->updatePret($id, $data);

            if ($success) {
                $this->successResponse('Prêt modifié avec succès');
            } else {
                $this->errorResponse('Aucune modification effectuée', 400);
            }
        } catch (InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la modification du prêt: ' . $e->getMessage(), 500);
        }
    }

    public function delete($id) {
        try {
            $success = $this->pretService->deletePret($id);

            if ($success) {
                $this->successResponse('Prêt supprimé avec succès');
            } else {
                $this->errorResponse('Prêt non trouvé', 404);
            }
        } catch (InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la suppression du prêt: ' . $e->getMessage(), 500);
        }
    }

    public function getRemboursements($id) {
        try {
            $remboursements = $this->pretService->getRemboursements($id);
            $this->jsonResponse($remboursements);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function addRemboursement($id) {
        try {
            $data = Flight::request()->data;
            $remboursementId = $this->pretService->addRemboursement($id, $data);
            $this->successResponse('Échéance de remboursement ajoutée', ['id' => $remboursementId]);
        } catch (InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de l\'ajout de l\'échéance: ' . $e->getMessage(), 500);
        }
    }

    public function markPayment($id) {
        try {
            $data = Flight::request()->data;
            $success = $this->pretService->markPayment($id, $data->montant_paye);

            if ($success) {
                $this->successResponse('Remboursement enregistré avec succès');
            } else {
                $this->errorResponse('Remboursement non trouvé', 404);
            }
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage(), 500);
        }
    }

    // Méthodes pour les vues (frontend)
    public function listView() {
        try {
            $prets = $this->pretService->getAllPrets();
            $this->renderView('prets/list', ['prets' => $prets]);
        } catch (Exception $e) {
            $this->renderError('Erreur lors du chargement des prêts');
        }
    }

    public function createView() {
        $this->renderView('prets/create');
    }

    public function editView($id) {
        try {
            $pret = $this->pretService->getPretById($id);
            if ($pret) {
                $this->renderView('prets/edit', ['pret' => $pret]);
            } else {
                $this->renderError('Prêt non trouvé');
            }
        } catch (Exception $e) {
            $this->renderError('Erreur lors du chargement du prêt');
        }
    }

    public function detailView($id) {
        try {
            $pret = $this->pretService->getPretById($id);
            $remboursements = $this->pretService->getRemboursements($id);

            if ($pret) {
                $this->renderView('prets/detail', [
                    'pret' => $pret,
                    'remboursements' => $remboursements
                ]);
            } else {
                $this->renderError('Prêt non trouvé');
            }
        } catch (Exception $e) {
            $this->renderError('Erreur lors du chargement du détail du prêt');
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
