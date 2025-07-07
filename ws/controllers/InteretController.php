<?php

class InteretController {

    /**
     * Calcule les intérêts pour une période donnée
     */
    public static function calculerInterets() {
        try {
            $data = Flight::request()->data;
            $annee = $data->annee ?? date('Y');
            $mois = $data->mois ?? date('n');

            InteretService::calculerInteretsMensuels($annee, $mois);
            Flight::json(['message' => "Intérêts calculés pour {$mois}/{$annee}"]);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors du calcul des intérêts: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Récupère les intérêts avec filtres
     */
    public static function getInterets() {
        try {
            $etablissementId = Flight::request()->query->etablissement_id ?? null;
            $anneeDebut = Flight::request()->query->annee_debut ?? null;
            $moisDebut = Flight::request()->query->mois_debut ?? null;
            $anneeFin = Flight::request()->query->annee_fin ?? null;
            $moisFin = Flight::request()->query->mois_fin ?? null;

            $interets = InteretService::getInteretsPeriode(
                $etablissementId, $anneeDebut, $moisDebut, $anneeFin, $moisFin
            );

            Flight::json($interets);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des intérêts: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Récupère les statistiques d'intérêts
     */
    public static function getStatistiques() {
        try {
            $etablissementId = Flight::request()->query->etablissement_id ?? null;
            $anneeDebut = Flight::request()->query->annee_debut ?? null;
            $moisDebut = Flight::request()->query->mois_debut ?? null;
            $anneeFin = Flight::request()->query->annee_fin ?? null;
            $moisFin = Flight::request()->query->mois_fin ?? null;

            $stats = InteretService::getStatistiquesInterets(
                $etablissementId, $anneeDebut, $moisDebut, $anneeFin, $moisFin
            );

            Flight::json($stats);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Données pour le graphique
     */
    public static function getChartData() {
        try {
            $etablissementId = Flight::request()->query->etablissement_id ?? null;
            $anneeDebut = Flight::request()->query->annee_debut ?? null;
            $moisDebut = Flight::request()->query->mois_debut ?? null;
            $anneeFin = Flight::request()->query->annee_fin ?? null;
            $moisFin = Flight::request()->query->mois_fin ?? null;

            $chartData = InteretService::getDataForChart(
                $etablissementId, $anneeDebut, $moisDebut, $anneeFin, $moisFin
            );

            Flight::json($chartData);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des données graphique: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Vue pour afficher la page des intérêts
     */
    public static function viewInterets() {
        Flight::json(['view' => 'interets/dashboard']);
    }
}
