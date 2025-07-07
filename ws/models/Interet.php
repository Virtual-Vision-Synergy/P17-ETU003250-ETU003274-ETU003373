<?php

class Interet
{

    /**
     * Obtient une connexion à la base de données
     */
    private static function getDB()
    {
        return getDB();
    }

    /**
     * Calcule les intérêts mensuels pour tous les établissements
     */
    public static function calculerInteretsMensuels($annee, $mois)
    {
        try {
            $db = self::getDB();
            $db->beginTransaction();

            // Récupérer tous les établissements
            $stmt = $db->query("SELECT id FROM s4_bank_etablissement");
            $etablissements = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($etablissements as $etablissementId) {
                self::calculerInteretsEtablissement($etablissementId, $annee, $mois);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception("Erreur lors du calcul des intérêts: " . $e->getMessage());
        }
    }

    /**
     * Calcule les intérêts pour un établissement spécifique
     */
    private static function calculerInteretsEtablissement($etablissementId, $annee, $mois)
    {
        $db = self::getDB();

        // Supprimer les anciens calculs pour cette période
        self::supprimerCalculsExistants($etablissementId, $annee, $mois);

        // Récupérer tous les prêts actifs pour cet établissement
        $stmt = $db->prepare("
            SELECT p.id, p.montant_accorde, p.taux_applique, p.duree_mois, p.date_demande,
                   COALESCE(SUM(r.montant_paye), 0) as montant_rembourse
            FROM s4_bank_pret p
            LEFT JOIN s4_bank_remboursement r ON p.id = r.pret_id AND r.statut = 'paye'
            WHERE p.etablissement_id = ? 
            AND p.statut IN ('approuve', 'en_cours')
            AND p.date_demande <= ?
            GROUP BY p.id
        ");

        $dateLimite = sprintf('%04d-%02d-%02d', $annee, $mois, date('t', mktime(0, 0, 0, $mois, 1, $annee)));
        $stmt->execute([$etablissementId, $dateLimite]);
        $prets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalInterets = 0;
        $nombrePrets = 0;
        $capitalTotal = 0;

        foreach ($prets as $pret) {
            $capitalRestant = self::calculerCapitalRestant($pret, $annee, $mois);
            $interets = self::calculerInteretsPret($pret, $annee, $mois);

            if ($interets > 0) {
                // Enregistrer le détail des intérêts par prêt
                self::enregistrerDetailInterets($etablissementId, $pret['id'], $annee, $mois, $capitalRestant, $pret['taux_applique'], $interets);
                $totalInterets += $interets;
                $nombrePrets++;
                $capitalTotal += $capitalRestant;
            }
        }

        // Enregistrer le résumé mensuel
        if ($totalInterets > 0) {
            self::enregistrerInteretsMensuels($etablissementId, $annee, $mois, $totalInterets, $nombrePrets, $capitalTotal);
        }
    }

    /**
     * Supprime les calculs existants pour une période donnée
     */
    private static function supprimerCalculsExistants($etablissementId, $annee, $mois)
    {
        $db = self::getDB();

        // Supprimer les détails
        $stmt = $db->prepare("DELETE FROM s4_bank_detail_interets WHERE etablissement_id = ? AND annee = ? AND mois = ?");
        $stmt->execute([$etablissementId, $annee, $mois]);

        // Supprimer le résumé mensuel
        $stmt = $db->prepare("DELETE FROM s4_bank_interets_mensuels WHERE etablissement_id = ? AND annee = ? AND mois = ?");
        $stmt->execute([$etablissementId, $annee, $mois]);
    }

    /**
     * Calcule les intérêts pour un prêt spécifique
     */
    private static function calculerInteretsPret($pret, $annee, $mois)
    {
        // Calculer le capital restant à rembourser
        $capitalRestant = self::calculerCapitalRestant($pret, $annee, $mois);

        // Calculer les intérêts sur le capital restant
        $tauxMensuel = $pret['taux_applique'] / 100 / 12;
        $interets = $capitalRestant * $tauxMensuel;

        return round($interets, 2);
    }

    /**
     * Enregistre le détail des intérêts pour un prêt
     */
    private static function enregistrerDetailInterets($etablissementId, $pretId, $annee, $mois, $capitalRestant, $taux, $montantInterets)
    {
        $db = self::getDB();
        $tauxMensuel = $taux / 100 / 12;

        $stmt = $db->prepare("
            INSERT INTO s4_bank_detail_interets (pret_id, etablissement_id, annee, mois, capital_restant, taux_mensuel, montant_interet, date_calcul)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$pretId, $etablissementId, $annee, $mois, $capitalRestant, $tauxMensuel, $montantInterets]);
    }

    /**
     * Enregistre le résumé mensuel des intérêts
     */
    private static function enregistrerInteretsMensuels($etablissementId, $annee, $mois, $montantInterets, $nombrePrets, $capitalTotal)
    {
        $db = self::getDB();

        $stmt = $db->prepare("
            INSERT INTO s4_bank_interets_mensuels (etablissement_id, annee, mois, montant_interets, nombre_prets_actifs, capital_total, date_calcul)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$etablissementId, $annee, $mois, $montantInterets, $nombrePrets, $capitalTotal]);
    }

    /**
     * Récupère les intérêts pour une période donnée
     */
    public static function getInteretsPeriode($etablissementId = null, $anneeDebut = null, $moisDebut = null, $anneeFin = null, $moisFin = null)
    {
        $db = self::getDB();

        $sql = "
            SELECT im.*, e.nom as etablissement_nom
            FROM s4_bank_interets_mensuels im
            INNER JOIN s4_bank_etablissement e ON im.etablissement_id = e.id
            WHERE 1=1
        ";

        $params = [];

        if ($etablissementId) {
            $sql .= " AND im.etablissement_id = ?";
            $params[] = $etablissementId;
        }

        if ($anneeDebut && $moisDebut) {
            $sql .= " AND (im.annee > ? OR (im.annee = ? AND im.mois >= ?))";
            $params[] = $anneeDebut;
            $params[] = $anneeDebut;
            $params[] = $moisDebut;
        }

        if ($anneeFin && $moisFin) {
            $sql .= " AND (im.annee < ? OR (im.annee = ? AND im.mois <= ?))";
            $params[] = $anneeFin;
            $params[] = $anneeFin;
            $params[] = $moisFin;
        }

        $sql .= " ORDER BY im.annee, im.mois";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les statistiques d'intérêts
     */
    public static function getStatistiquesInterets($etablissementId = null, $anneeDebut = null, $moisDebut = null, $anneeFin = null, $moisFin = null)
    {
        $interets = self::getInteretsPeriode($etablissementId, $anneeDebut, $moisDebut, $anneeFin, $moisFin);

        $totalInterets = array_sum(array_column($interets, 'montant_interets'));
        $moyenneInterets = count($interets) > 0 ? $totalInterets / count($interets) : 0;
        $maxInterets = count($interets) > 0 ? max(array_column($interets, 'montant_interets')) : 0;
        $minInterets = count($interets) > 0 ? min(array_column($interets, 'montant_interets')) : 0;
        $totalCapital = array_sum(array_column($interets, 'capital_total'));
        $totalPrets = array_sum(array_column($interets, 'nombre_prets_actifs'));

        return [
            'total_interets' => round($totalInterets, 2),
            'moyenne_interets' => round($moyenneInterets, 2),
            'max_interets' => round($maxInterets, 2),
            'min_interets' => round($minInterets, 2),
            'nombre_periodes' => count($interets),
            'total_capital' => round($totalCapital, 2),
            'total_prets_actifs' => $totalPrets,
            'donnees' => $interets
        ];
    }

    /**
     * Prépare les données pour le graphique
     */
    public static function getDataForChart($etablissementId = null, $anneeDebut = null, $moisDebut = null, $anneeFin = null, $moisFin = null)
    {
        $interets = self::getInteretsPeriode($etablissementId, $anneeDebut, $moisDebut, $anneeFin, $moisFin);

        $labels = [];
        $values = [];

        foreach ($interets as $interet) {
            $labels[] = sprintf('%04d-%02d', $interet['annee'], $interet['mois']);
            $values[] = floatval($interet['montant_interets']);
        }

        return [
            'labels' => $labels,
            'data' => $values
        ];
    }

    /**
     * Calcule le capital restant à rembourser
     */
    private static function calculerCapitalRestant($pret, $annee, $mois)
    {
        $montantAccorde = $pret['montant_accorde'];
        $montantRembourse = $pret['montant_rembourse'];

        // Calculer le capital remboursé (sans les intérêts)
        $tauxMensuel = $pret['taux_applique'] / 100 / 12;

        if ($tauxMensuel == 0) {
            // Prêt sans intérêt
            $dateDebut = new DateTime($pret['date_demande']);
            $datePeriode = new DateTime(sprintf('%04d-%02d-01', $annee, $mois));
            $moisEcoules = $dateDebut->diff($datePeriode)->m + ($dateDebut->diff($datePeriode)->y * 12);

            $mensualite = $montantAccorde / $pret['duree_mois'];
            $capitalRembourse = min($moisEcoules * $mensualite, $montantAccorde);
        } else {
            // Prêt avec intérêt
            $mensualite = ($montantAccorde * $tauxMensuel * pow(1 + $tauxMensuel, $pret['duree_mois'])) /
                (pow(1 + $tauxMensuel, $pret['duree_mois']) - 1);

            // Estimer le capital remboursé basé sur le nombre de paiements
            $dateDebut = new DateTime($pret['date_demande']);
            $datePeriode = new DateTime(sprintf('%04d-%02d-01', $annee, $mois));
            $moisEcoules = $dateDebut->diff($datePeriode)->m + ($dateDebut->diff($datePeriode)->y * 12);

            $capitalRembourse = 0;
            for ($i = 1; $i <= $moisEcoules && $i <= $pret['duree_mois']; $i++) {
                $interetMensuel = ($montantAccorde - $capitalRembourse) * $tauxMensuel;
                $capitalMensuel = $mensualite - $interetMensuel;
                $capitalRembourse += $capitalMensuel;
            }
        }

        return max(0, $montantAccorde - $capitalRembourse);
    }
}
