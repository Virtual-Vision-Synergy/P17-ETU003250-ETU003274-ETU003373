<?php
require 'vendor/autoload.php';
require 'db.php';

// Inclusion des fichiers de routes par fonctionnalité
require 'etudiants.php';
require 'prets.php';
require 'types-prets.php';
require 'etablissements.php';

Flight::start();
