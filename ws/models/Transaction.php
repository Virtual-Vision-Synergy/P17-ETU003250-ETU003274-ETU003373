<?php

class TransactionService
{


    public static function getAllTransactions()
    {
        $db = getDB();
        $stmt = $db->query("
            SELECT t.*, ef.nom as etablissement_nom
            FROM s4_bank_transaction t
            JOIN s4_bank_etablissement ef ON t.etablissement_id = ef.id
            ORDER BY t.date_transaction DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTransactionById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT t.*, ef.nom as etablissement_nom
            FROM s4_bank_transaction t
            JOIN s4_bank_etablissement ef ON t.etablissement_id = ef.id
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getTransactionsByEtablissement($etablissementId)
    {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT t.*, ef.nom as etablissement_nom
            FROM s4_bank_transaction t
            JOIN s4_bank_etablissement ef ON t.etablissement_id = ef.id
            WHERE t.etablissement_id = ?
            ORDER BY t.date_transaction DESC
        ");
        $stmt->execute([$etablissementId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTransactionsByType($type)
    {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT t.*, ef.nom as etablissement_nom
            FROM s4_bank_transaction t
            JOIN s4_bank_etablissement ef ON t.etablissement_id = ef.id
            WHERE t.type_transaction = ?
            ORDER BY t.date_transaction DESC
        ");
        $stmt->execute([$type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLastTransactionsByMonth()
    {
        $db = getDB();
        $stmt = $db->query("
            SELECT DATE_FORMAT(t.date_transaction, '%Y-%m') AS month, t.solde_apres AS last_solde_apres, ef.nom AS etablissement_name
            FROM s4_bank_transaction t
            JOIN s4_bank_etablissement ef ON t.etablissement_id = ef.id
            WHERE t.date_transaction = (
                SELECT MAX(t2.date_transaction)
                FROM s4_bank_transaction t2
                WHERE DATE_FORMAT(t2.date_transaction, '%Y-%m') = DATE_FORMAT(t.date_transaction, '%Y-%m')
            )
            ORDER BY month DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
