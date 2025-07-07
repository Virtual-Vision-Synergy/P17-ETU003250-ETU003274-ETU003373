<?php
require_once 'controllers/EtudiantController.php';

// Routes API pour les étudiants
Flight::route('GET /etudiants', function() {
    $controller = new EtudiantController();
    $controller->index();
});

Flight::route('GET /etudiants/@id', function($id) {
    $controller = new EtudiantController();
    $controller->show($id);
});

Flight::route('POST /etudiants', function() {
    $controller = new EtudiantController();
    $controller->store();
});

Flight::route('PUT /etudiants/@id', function($id) {
    $controller = new EtudiantController();
    $controller->update($id);
});

Flight::route('DELETE /etudiants/@id', function($id) {
    $controller = new EtudiantController();
    $controller->delete($id);
});

// Routes pour les vues (frontend)
Flight::route('GET /etudiants/list', function() {
    $controller = new EtudiantController();
    $controller->listView();
});

Flight::route('GET /etudiants/create', function() {
    $controller = new EtudiantController();
    $controller->createView();
});

Flight::route('GET /etudiants/@id/edit', function($id) {
    $controller = new EtudiantController();
    $controller->editView($id);
});
