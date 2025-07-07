<?php

class TransactionController {

    public static function getAll() {
        try {
            $transactions = TransactionService::getAllTransactions();
            Flight::json($transactions);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des transactions: ' . $e->getMessage()], 500);
        }
    }

    public static function getById($id) {
        try {
            $transaction = TransactionService::getTransactionById($id);
            if ($transaction) {
                Flight::json($transaction);
            } else {
                Flight::json(['error' => 'Transaction non trouvée'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération de la transaction: ' . $e->getMessage()], 500);
        }
    }

    public static function getByEtablissement($etablissementId) {
        try {
            $transactions = TransactionService::getTransactionsByEtablissement($etablissementId);
            Flight::json($transactions);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des transactions: ' . $e->getMessage()], 500);
        }
    }

    public static function getByType($type) {
        try {
            $transactions = TransactionService::getTransactionsByType($type);
            Flight::json($transactions);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des transactions: ' . $e->getMessage()], 500);
        }
    }
}
