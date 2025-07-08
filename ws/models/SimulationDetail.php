<?php


class SimulationDetailService
{
    public static function getAllBySimulationId($simulationId)
    {
        $db = getDB();
        $query = "SELECT * FROM s4_bank_simulation_detail WHERE simulation_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$simulationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertAll($simulationId, $details)
    {
        $db = getDB();
        $query = "INSERT INTO s4_bank_simulation_detail (simulation_id, echeance, capital_restant_debut, annuite, interet, capital_rembourse, capital_restant_fin) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);

        foreach ($details as $detail) {
            $stmt->execute([
                $simulationId,
                $detail['echeance'],
                $detail['capital_restant_debut'],
                $detail['annuite'],
                $detail['interet'],
                $detail['capital_rembourse'],
                $detail['capital_restant_fin']
            ]);
        }

        return true;
    }
}
