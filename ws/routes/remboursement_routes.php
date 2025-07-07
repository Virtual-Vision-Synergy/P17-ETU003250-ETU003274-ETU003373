<?php

// Routes pour la gestion des remboursements

// Récupérer tous les remboursements
Flight::route('GET /remboursements', ['RemboursementController', 'getAllRemboursements']);

// Récupérer les remboursements d'un prêt spécifique
Flight::route('GET /remboursements/pret/@pretId', ['RemboursementController', 'getRemboursementsByPret']);

// Générer le tableau d'amortissement pour un prêt
Flight::route('POST /remboursements/generer-tableau', ['RemboursementController', 'genererTableauAmortissement']);

// Effectuer un paiement de remboursement
Flight::route('POST /remboursements/paiement', ['RemboursementController', 'effectuerPaiement']);

// Calculer l'annuité constante
Flight::route('POST /remboursements/calculer-annuite', ['RemboursementController', 'calculerAnnuite']);

// Récupérer le détail d'une échéance
Flight::route('GET /remboursements/detail/@pretId/@numeroEcheance', ['RemboursementController', 'getDetailEcheance']);

// Récupérer les remboursements en retard
Flight::route('GET /remboursements/retard', ['RemboursementController', 'getRemboursementsEnRetard']);

// Simuler un prêt avec tableau d'amortissement
Flight::route('POST /remboursements/simuler', ['RemboursementController', 'simulerPret']);
