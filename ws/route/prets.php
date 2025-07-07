<?php
require_once 'controllers/PretController.php';

// Routes API pour les prêts
Flight::route('GET /prets', function() {
    $controller = new PretController();
    $controller->index();
});

Flight::route('GET /prets/@id', function($id) {
    $controller = new PretController();
    $controller->show($id);
});

Flight::route('POST /prets', function() {
    $controller = new PretController();
    $controller->store();
});

Flight::route('PUT /prets/@id', function($id) {
    $controller = new PretController();
    $controller->update($id);
});

Flight::route('DELETE /prets/@id', function($id) {
    $controller = new PretController();
    $controller->delete($id);
});

// Routes pour les remboursements
Flight::route('GET /prets/@id/remboursements', function($id) {
    $controller = new PretController();
    $controller->getRemboursements($id);
});

Flight::route('POST /prets/@id/remboursements', function($id) {
    $controller = new PretController();
    $controller->addRemboursement($id);
});

Flight::route('PUT /remboursements/@id/payer', function($id) {
    $controller = new PretController();
    $controller->markPayment($id);
});

// Routes pour les vues (frontend)
Flight::route('GET /prets/list', function() {
    $controller = new PretController();
    $controller->listView();
});

Flight::route('GET /prets/create', function() {
    $controller = new PretController();
    $controller->createView();
});

Flight::route('GET /prets/@id/edit', function($id) {
    $controller = new PretController();
    $controller->editView($id);
});

Flight::route('GET /prets/@id/detail', function($id) {
    $controller = new PretController();
    $controller->detailView($id);
});
