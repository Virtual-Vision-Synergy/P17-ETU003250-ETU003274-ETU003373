<?php
require_once 'controllers/EtablissementController.php';

// Routes API pour les établissements
Flight::route('GET /etablissements', function() {
    $controller = new EtablissementController();
    $controller->index();
});

Flight::route('GET /etablissements/@id', function($id) {
    $controller = new EtablissementController();
    $controller->show($id);
});

Flight::route('POST /etablissements', function() {
    $controller = new EtablissementController();
    $controller->store();
});

Flight::route('PUT /etablissements/@id', function($id) {
    $controller = new EtablissementController();
    $controller->update($id);
});

Flight::route('DELETE /etablissements/@id', function($id) {
    $controller = new EtablissementController();
    $controller->delete($id);
});

// Routes pour la gestion des fonds
Flight::route('PUT /etablissements/@id/fonds', function($id) {
    $controller = new EtablissementController();
    $controller->updateFonds($id);
});

Flight::route('POST /etablissements/@id/fonds/ajouter', function($id) {
    $controller = new EtablissementController();
    $controller->addFonds($id);
});

Flight::route('GET /etablissements/@id/statistiques', function($id) {
    $controller = new EtablissementController();
    $controller->getStatistiques($id);
});

// Routes pour les vues (frontend)
Flight::route('GET /etablissements/list', function() {
    $controller = new EtablissementController();
    $controller->listView();
});

Flight::route('GET /etablissements/create', function() {
    $controller = new EtablissementController();
    $controller->createView();
});

Flight::route('GET /etablissements/@id/edit', function($id) {
    $controller = new EtablissementController();
    $controller->editView($id);
});

Flight::route('GET /etablissements/@id/dashboard', function($id) {
    $controller = new EtablissementController();
    $controller->dashboardView($id);
});
