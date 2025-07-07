<?php
require_once 'controllers/TypePretController.php';

// Routes API pour les types de prêts
Flight::route('GET /types-prets', function() {
    $controller = new TypePretController();
    $controller->index();
});

Flight::route('GET /types-prets/@id', function($id) {
    $controller = new TypePretController();
    $controller->show($id);
});

Flight::route('POST /types-prets', function() {
    $controller = new TypePretController();
    $controller->store();
});

Flight::route('PUT /types-prets/@id', function($id) {
    $controller = new TypePretController();
    $controller->update($id);
});

Flight::route('DELETE /types-prets/@id', function($id) {
    $controller = new TypePretController();
    $controller->delete($id);
});

Flight::route('PUT /types-prets/@id/desactiver', function($id) {
    $controller = new TypePretController();
    $controller->deactivate($id);
});

Flight::route('POST /types-prets/validate-loan', function() {
    $controller = new TypePretController();
    $controller->validateLoan();
});

// Routes pour les vues (frontend)
Flight::route('GET /types-prets/list', function() {
    $controller = new TypePretController();
    $controller->listView();
});

Flight::route('GET /types-prets/create', function() {
    $controller = new TypePretController();
    $controller->createView();
});

Flight::route('GET /types-prets/@id/edit', function($id) {
    $controller = new TypePretController();
    $controller->editView($id);
});
