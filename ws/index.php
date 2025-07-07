<?php
require 'vendor/autoload.php';
require 'db.php';

// Chargement des services métiers
require 'models/Etudiant.php';
require 'models/Etablissement.php';
require 'models/TypePret.php';
require 'models/Pret.php';
require 'models/Transaction.php';

// Chargement des contrôleurs
require 'controllers/EtudiantController.php';
require 'controllers/EtablissementController.php';
require 'controllers/TypePretController.php';
require 'controllers/PretController.php';
require 'controllers/TransactionController.php';

// Chargement des routes
require 'routes/etudiant_routes.php';
require 'routes/etablissement_routes.php';
require 'routes/types-pret_routes.php';
require 'routes/pret_routes.php';
require 'routes/transaction_routes.php';

Flight::start();
