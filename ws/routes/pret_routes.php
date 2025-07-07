<?php

// Routes pour les prêts
Flight::route('GET /prets', ['PretController', 'getAll']);
Flight::route('GET /prets/@id', ['PretController', 'getById']);
Flight::route('POST /prets', ['PretController', 'create']);
Flight::route('POST /prets/@id/approve', ['PretController', 'approveOrReject']);
