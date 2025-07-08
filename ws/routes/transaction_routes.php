<?php

// Routes pour les transactions
Flight::route('GET /transactions', ['TransactionController', 'getAll']);
Flight::route('GET /transactions/last', ['TransactionController', 'getLast']);
Flight::route('GET /transactions/@id', ['TransactionController', 'getById']);
Flight::route('GET /etablissements/@id/transactions', ['TransactionController', 'getByEtablissement']);
Flight::route('GET /transactions/type/@type', ['TransactionController', 'getByType']);
