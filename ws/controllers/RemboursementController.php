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
     * Effectue un paiement de remboursement automatique
     */
    public static function effectuerPaiement()
    {
        try {
            $data = json_decode(Flight::request()->getBody(), true);

            if (!$data || !isset($data['remboursement_id'])) {
                throw new InvalidArgumentException('ID du remboursement requis');
            }

            // Le montant est maintenant ignoré - il sera automatiquement fixé à l'annuité
            $montantPaye = isset($data['montant_paye']) ? $data['montant_paye'] : null;

            $result = RemboursementService::effectuerPaiement(
                $data['remboursement_id'],
                $montantPaye // Ce paramètre sera ignoré par le service
            );

            Flight::json([
                'success' => true,
                'message' => 'Paiement automatique effectué avec succès',
                'data' => $result
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

            $result = RemboursementService::simulerPret(
                $data->capital,
                $data->taux_annuel,
                $data->duree_mois
            );

            Flight::json([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les prêts valides pour la simulation
     */
    public static function getPretsValides()
    {
        try {
            $pretsValides = RemboursementService::getPretsValides();
            Flight::json([
                'success' => true,
                'data' => $pretsValides
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simule un prêt existant avec ses données réelles
     */
    public static function simulerPretExistant()
    {
        try {
            $data = json_decode(Flight::request()->getBody());

            if (!$data || !isset($data->pret_id)) {
                throw new InvalidArgumentException('ID du prêt requis');
            }

            $simulation = RemboursementService::simulerPretExistant($data->pret_id);
            Flight::json([
                'success' => true,
                'data' => $simulation
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
