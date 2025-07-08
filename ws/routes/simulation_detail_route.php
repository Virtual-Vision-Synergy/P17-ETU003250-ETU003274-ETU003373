<?php

Flight::route('GET /simulations/@id/details', ['SimulationDetailController', 'getAllBySimulationId']);
Flight::route('POST /simulations/details', ['SimulationDetailController', 'insertAll']);
