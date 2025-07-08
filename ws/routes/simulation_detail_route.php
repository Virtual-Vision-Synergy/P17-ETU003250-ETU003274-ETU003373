<?php

Flight::route('POST /simulations/details', ['SimulationDetailController', 'insertAll']);
Flight::route('GET /simulations/@id/details', ['SimulationDetailController', 'getAllBySimulationId']);
