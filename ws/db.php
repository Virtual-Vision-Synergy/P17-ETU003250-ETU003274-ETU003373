<?php
function getDB() {
    $host = 'localhost';
    $dbname ='db_s2_ETU003250';
    $username = 'ETU003250';
    $password = '7Hyb7tZL';

    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}


