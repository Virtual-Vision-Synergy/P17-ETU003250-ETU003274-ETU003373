<?php

Flight::route('GET /simulations', ['SimulationController', 'getAll']);
Flight::route('POST /simulations', ['SimulationController', 'create']);
