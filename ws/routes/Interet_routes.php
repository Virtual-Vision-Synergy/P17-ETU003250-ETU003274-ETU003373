<?php

// Routes pour les intérêts
Flight::route('POST /interets/calculer', ['InteretController', 'calculerInterets']);
Flight::route('GET /interets', ['InteretController', 'getInterets']);
Flight::route('GET /interets/statistiques', ['InteretController', 'getStatistiques']);
Flight::route('GET /interets/chart-data', ['InteretController', 'getChartData']);
Flight::route('GET /interets/dashboard', ['InteretController', 'viewInterets']);


