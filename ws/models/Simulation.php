<?php



class SimulationService
{
    public static function insert($annuiteMensuelle, $montantTotal, $coutCredit, $duree)
    {
        $db = getDB();
        $query = "INSERT INTO s4_bank_simulation (annuite_mensuelle, montant_total, cout_credit, duree) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$annuiteMensuelle, $montantTotal, $coutCredit, $duree]);
        return $db->lastInsertId();
    }

    public static function getAll()
    {
        $db = getDB();
        $query = "SELECT * FROM s4_bank_simulation ORDER BY id";
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
