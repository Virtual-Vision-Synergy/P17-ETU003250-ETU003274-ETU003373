<?php

// Routes pour les prêts
Flight::route('GET /prets', ['PretController', 'getAll']);
Flight::route('GET /prets/in-process', ['PretController', 'getAllInProcess']);
Flight::route('GET /prets/@id', ['PretController', 'getById']);
Flight::route('POST /prets', ['PretController', 'create']);
Flight::route('POST /prets/@id/approve', ['PretController', 'approve']);

// Routes pour la génération de PDF
Flight::route('GET /prets/@id/pdf', ['PretController', 'generatePdf']);
Flight::route('GET /prets/@id/pdf/view', ['PretController', 'generatePdfInline']);
