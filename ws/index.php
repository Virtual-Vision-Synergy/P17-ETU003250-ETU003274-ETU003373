<?php
require 'vendor/autoload.php';
require 'db.php';

// Chargement des helpers métiers
require 'models/Etudiant.php';
require 'models/Etablissement.php';
require 'models/TypePret.php';
require 'models/Pret.php';
require 'models/Remboursement.php';
require 'models/Transaction.php';
require 'models/Interet.php';
require 'models/Simulation.php';
require 'models/SimulationDetail.php';

// Chargement des contrôleurs
require 'controllers/EtudiantController.php';
require 'controllers/EtablissementController.php';
require 'controllers/TypePretController.php';
require 'controllers/PretController.php';
require 'controllers/RemboursementController.php';
require 'controllers/TransactionController.php';
require 'controllers/InteretController.php';
require 'controllers/SimulationController.php';
require 'controllers/SimulationDetailController.php';

// Chargement des routes
require 'routes/etudiant_routes.php';
require 'routes/etablissement_routes.php';
require 'routes/types-pret_routes.php';
require 'routes/pret_routes.php';
require 'routes/remboursement_routes.php';
require 'routes/transaction_routes.php';
require 'routes/Interet_routes.php';
require 'routes/simulation_route.php';
require 'routes/simulation_detail_route.php';

Flight::start();
