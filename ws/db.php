<?php
function getDB() {
    $host = 'localhost';
    $dbname ='Examen_final_s4';
    $username = 'ETU003373';
    $password = 'ETU003373';

    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}


