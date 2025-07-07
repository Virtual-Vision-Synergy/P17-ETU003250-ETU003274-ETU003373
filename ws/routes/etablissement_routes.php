<?php

// Routes pour les établissements financiers
Flight::route('GET /etablissements', ['EtablissementController', 'getAll']);
Flight::route('GET /etablissements/@id', ['EtablissementController', 'getById']);
Flight::route('POST /etablissements', ['EtablissementController', 'create']);
Flight::route('POST /etablissements/@id/depot', ['EtablissementController', 'depot']);
