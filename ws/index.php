<?php
require 'vendor/autoload.php';
require 'db.php';

// Inclusion des fichiers de routes par fonctionnalité
require 'route/etudiants.php';
require 'route/prets.php';
require 'route/types-prets.php';
require 'route/etablissements.php';

Flight::start();
