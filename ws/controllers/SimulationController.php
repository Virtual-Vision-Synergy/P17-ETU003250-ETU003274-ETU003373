<?php

class SimulationController {

    public static function
    getAll() {
        try {
            $simulations = SimulationService::getAll();
            Flight::json($simulations);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des simulations: ' . $e->getMessage()], 500);
        }
    }

    public static function create() {
        try {
            $data = Flight::request()->data;

            $id = SimulationService::insert(
                $data->annuite_mensuelle,
                $data->montant_total,
                $data->cout_credit,
                $data->duree
            );

            Flight::json(['message' => 'Simulation ajoutée', 'id' => $id], 201);
        } catch (PDOException $e) {
            Flight::json(['error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()], 400);
        }
    }
}
