<?php

class RemboursementController
{
    /**
     * Récupère tous les remboursements
     */
    public static function getAllRemboursements()
    {
        try {
            $remboursements = RemboursementService::getAllRemboursements();
            Flight::json([
                'success' => true,
                'data' => $remboursements
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les remboursements d'un prêt spécifique
     */
    public static function getRemboursementsByPret($pretId)
    {
        try {
            $remboursements = RemboursementService::getRemboursementsByPret($pretId);
            Flight::json([
                'success' => true,
                'data' => $remboursements
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère le tableau d'amortissement pour un prêt
     */
    public static function genererTableauAmortissement()
    {
        try {
            $data = json_decode(Flight::request()->getBody());

            if (!$data || !isset($data->pret_id) || !isset($data->capital) ||
                !isset($data->taux_annuel) || !isset($data->duree_mois) || !isset($data->date_debut)) {
                throw new InvalidArgumentException('Données manquantes pour générer le tableau d\'amortissement');
            }

            $result = RemboursementService::genererTableauAmortissement(
                $data->pret_id,
                $data->capital,
                $data->taux_annuel,
                $data->duree_mois,
                $data->date_debut
            );

            Flight::json([
                'success' => true,
                'message' => 'Tableau d\'amortissement généré avec succès'
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Effectue un paiement de remboursement
     */
    public static function effectuerPaiement()
    {
        try {
            $data = json_decode(Flight::request()->getBody());

            if (!$data || !isset($data->remboursement_id) || !isset($data->montant_paye)) {
                throw new InvalidArgumentException('ID du remboursement et montant payé requis');
            }

            if ($data->montant_paye <= 0) {
                throw new InvalidArgumentException('Le montant payé doit être positif');
            }

            $result = RemboursementService::effectuerPaiement(
                $data->remboursement_id,
                $data->montant_paye
            );

            Flight::json([
                'success' => true,
                'message' => 'Paiement effectué avec succès'
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcule l'annuité constante
     */
    public static function calculerAnnuite()
    {
        try {
            $data = json_decode(Flight::request()->getBody());

            if (!$data || !isset($data->capital) || !isset($data->taux_annuel) || !isset($data->duree_mois)) {
                throw new InvalidArgumentException('Capital, taux annuel et durée en mois requis');
            }

            $annuite = RemboursementService::calculerAnnuite(
                $data->capital,
                $data->taux_annuel,
                $data->duree_mois
            );

            Flight::json([
                'success' => true,
                'data' => [
                    'annuite' => $annuite,
                    'montant_total' => round($annuite * $data->duree_mois, 2),
                    'cout_credit' => round(($annuite * $data->duree_mois) - $data->capital, 2)
                ]
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère le détail d'une échéance
     */
    public static function getDetailEcheance($pretId, $numeroEcheance)
    {
        try {
            $detail = RemboursementService::calculerDetailEcheance($pretId, $numeroEcheance);

            Flight::json([
                'success' => true,
                'data' => $detail
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les remboursements en retard
     */
    public static function getRemboursementsEnRetard()
    {
        try {
            $remboursements = RemboursementService::getRemboursementsEnRetard();
            Flight::json([
                'success' => true,
                'data' => $remboursements
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les remboursements non payés pour le select
     */
    public static function getRemboursementsNonPayes()
    {
        try {
            $remboursements = RemboursementService::getRemboursementsNonPayes();
            Flight::json([
                'success' => true,
                'data' => $remboursements
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simulation d'un prêt avec tableau d'amortissement
     */
    public static function simulerPret()
    {
        try {
            $data = json_decode(Flight::request()->getBody());

            if (!$data || !isset($data->capital) || !isset($data->taux_annuel) || !isset($data->duree_mois)) {
                throw new InvalidArgumentException('Capital, taux annuel et durée en mois requis');
            }

            $capital = $data->capital;
            $tauxAnnuel = $data->taux_annuel;
            $dureeMois = $data->duree_mois;
            $tauxMensuel = $tauxAnnuel / 100 / 12;

            $annuite = RemboursementService::calculerAnnuite($capital, $tauxAnnuel, $dureeMois);

            $tableau = [];
            $capitalRestant = $capital;

            for ($i = 1; $i <= $dureeMois; $i++) {
                $interets = $capitalRestant * $tauxMensuel;
                $capitalRembourse = $annuite - $interets;

                // Ajustement pour la dernière échéance
                if ($i == $dureeMois) {
                    $capitalRembourse = $capitalRestant;
                    $annuite_ajustee = $capitalRemburse + $interets;
                } else {
                    $annuite_ajustee = $annuite;
                }

                $tableau[] = [
                    'numero_echeance' => $i,
                    'capital_restant_debut' => round($capitalRestant, 2),
                    'annuite' => round($annuite_ajustee, 2),
                    'interets' => round($interets, 2),
                    'capital_rembourse' => round($capitalRembourse, 2),
                    'capital_restant_fin' => round($capitalRestant - $capitalRembourse, 2)
                ];

                $capitalRestant -= $capitalRembourse;
            }

            Flight::json([
                'success' => true,
                'data' => [
                    'simulation' => [
                        'capital' => $capital,
                        'taux_annuel' => $tauxAnnuel,
                        'duree_mois' => $dureeMois,
                        'annuite' => $annuite,
                        'montant_total' => round($annuite * $dureeMois, 2),
                        'cout_credit' => round(($annuite * $dureeMois) - $capital, 2)
                    ],
                    'tableau_amortissement' => $tableau
                ]
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
