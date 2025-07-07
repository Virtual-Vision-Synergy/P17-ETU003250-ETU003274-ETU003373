<?php
require 'vendor/autoload.php';
require 'db.php';

Flight::route('GET /etudiants', function() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_bank_etudiant");
    Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
});

Flight::route('GET /etudiants/@id', function($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_bank_etudiant WHERE id = ?");
    $stmt->execute([$id]);
    Flight::json($stmt->fetch(PDO::FETCH_ASSOC));
});

Flight::route('POST /etudiants', function() {
    $data = Flight::request()->data;
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO s4_bank_etudiant (nom, prenom, email, age) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age]);
    Flight::json(['message' => 'Étudiant ajouté', 'id' => $db->lastInsertId()]);
});

Flight::route('PUT /etudiants/@id', function($id) {
    $data = Flight::request()->data;
    $db = getDB();
    $stmt = $db->prepare("UPDATE s4_bank_etudiant SET nom = ?, prenom = ?, email = ?, age = ? WHERE id = ?");
    $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age, $id]);
    Flight::json(['message' => 'Étudiant modifié']);
});

Flight::route('DELETE /etudiants/@id', function($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_bank_etudiant WHERE id = ?");
    $stmt->execute([$id]);
    Flight::json(['message' => 'Étudiant supprimé']);
});

Flight::start();