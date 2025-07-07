<?php
require 'vendor/autoload.php';
require 'db.php';

// Chargement des services métiers
require 'services/EtudiantService.php';
require 'services/EtablissementService.php';
require 'services/TypePretService.php';
require 'services/PretService.php';
require 'services/TransactionService.php';
require 'services/InteretService.php';

// Chargement des contrôleurs
require 'controllers/EtudiantController.php';
require 'controllers/EtablissementController.php';
require 'controllers/TypePretController.php';
require 'controllers/PretController.php';
require 'controllers/TransactionController.php';
require 'controllers/InteretController.php';

// Chargement des routes
require 'routes/etudiants.php';
require 'routes/etablissements.php';
require 'routes/types-prets.php';
require 'routes/prets.php';
require 'routes/transactions.php';
require 'routes/interets.php';

Flight::start();
