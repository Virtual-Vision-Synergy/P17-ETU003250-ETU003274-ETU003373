<?php

class SimulationDetailController {

    public static function getAllBySimulationId($simulationId) {
        try {
            $details = SimulationDetailService::getAllBySimulationId($simulationId);
            Flight::json($details);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des détails de simulation: ' . $e->getMessage()], 500);
        }
    }

    public static function insertAll() {
        try {
            $data = Flight::request()->data;

            SimulationDetailService::insertAll($data->simulation_id, $data->details);

            Flight::json(['message' => 'Détails de simulation ajoutés'], 201);
        } catch (PDOException $e) {
            Flight::json(['error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()], 400);
        }
    }
}
