<?php
require 'vendor/autoload.php';
require 'db.php';

// ========== ROUTES ÉTUDIANTS ==========
Flight::route('GET /etudiants', function() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_bank_etudiant ORDER BY id");
    Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
});

Flight::route('GET /etudiants/@id', function($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_bank_etudiant WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        Flight::json($result);
    } else {
        Flight::json(['error' => 'Étudiant non trouvé'], 404);
    }
});

Flight::route('POST /etudiants', function() {
    $data = Flight::request()->data;
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO s4_bank_etudiant (nom, prenom, email, age, telephone, adresse) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age, $data->telephone ?? null, $data->adresse ?? null]);
        Flight::json(['message' => 'Étudiant ajouté', 'id' => $db->lastInsertId()]);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()], 400);
    }
});

Flight::route('PUT /etudiants/@id', function($id) {
    $data = Flight::request()->data;
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE s4_bank_etudiant SET nom = ?, prenom = ?, email = ?, age = ?, telephone = ?, adresse = ? WHERE id = ?");
        $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age, $data->telephone ?? null, $data->adresse ?? null, $id]);
        Flight::json(['message' => 'Étudiant modifié']);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Erreur lors de la modification: ' . $e->getMessage()], 400);
    }
});

Flight::route('DELETE /etudiants/@id', function($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_bank_etudiant WHERE id = ?");
    $stmt->execute([$id]);
    Flight::json(['message' => 'Étudiant supprimé']);
});

// ========== ROUTES ÉTABLISSEMENTS FINANCIERS ==========
Flight::route('GET /etablissements', function() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_bank_etablissement ORDER BY id");
    Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
});

Flight::route('GET /etablissements/@id', function($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_bank_etablissement WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        Flight::json($result);
    } else {
        Flight::json(['error' => 'Établissement non trouvé'], 404);
    }
});

Flight::route('POST /etablissements', function() {
    $data = Flight::request()->data;
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO s4_bank_etablissement (nom, adresse, telephone, email, fonds_disponibles) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data->nom, $data->adresse ?? null, $data->telephone ?? null, $data->email ?? null, $data->fonds_disponibles ?? 0]);
        Flight::json(['message' => 'Établissement créé', 'id' => $db->lastInsertId()]);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 400);
    }
});

// Ajouter des fonds à un établissement
Flight::route('POST /etablissements/@id/depot', function($id) {
    $data = Flight::request()->data;
    $montant = $data->montant;

    if ($montant <= 0) {
        Flight::json(['error' => 'Le montant doit être positif'], 400);
        return;
    }

    try {
        $db = getDB();
        $db->beginTransaction();

        // Récupérer le solde actuel
        $stmt = $db->prepare("SELECT fonds_disponibles FROM s4_bank_etablissement WHERE id = ?");
        $stmt->execute([$id]);
        $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$etablissement) {
            $db->rollback();
            Flight::json(['error' => 'Établissement non trouvé'], 404);
            return;
        }

        $soldeAvant = $etablissement['fonds_disponibles'];
        $soldeApres = $soldeAvant + $montant;

        // Mettre à jour les fonds
        $stmt = $db->prepare("UPDATE s4_bank_etablissement SET fonds_disponibles = ? WHERE id = ?");
        $stmt->execute([$soldeApres, $id]);

        // Enregistrer la transaction
        $stmt = $db->prepare("INSERT INTO s4_bank_transaction (etablissement_id, type_transaction, montant, solde_avant, solde_apres, description) VALUES (?, 'depot', ?, ?, ?, ?)");
        $stmt->execute([$id, $montant, $soldeAvant, $soldeApres, $data->description ?? 'Dépôt de fonds']);

        $db->commit();
        Flight::json(['message' => 'Fonds ajoutés', 'nouveau_solde' => $soldeApres]);
    } catch (PDOException $e) {
        $db->rollback();
        Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 500);
    }
});

// ========== ROUTES TYPES DE PRÊTS ==========
Flight::route('GET /types-prets', function() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_bank_type_pret ORDER BY taux_interet");
    Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
});

Flight::route('GET /types-prets/@id', function($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_bank_type_pret WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        Flight::json($result);
    } else {
        Flight::json(['error' => 'Type de prêt non trouvé'], 404);
    }
});

Flight::route('POST /types-prets', function() {
    $data = Flight::request()->data;
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO s4_bank_type_pret (nom, description, taux_interet, duree_max_mois, montant_min, montant_max, actif) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->description ?? null,
            $data->taux_interet,
            $data->duree_max_mois,
            $data->montant_min ?? 0,
            $data->montant_max ?? null,
            $data->actif ?? true
        ]);
        Flight::json(['message' => 'Type de prêt créé', 'id' => $db->lastInsertId()]);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 400);
    }
});

Flight::route('PUT /types-prets/@id', function($id) {
    $data = Flight::request()->data;
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE s4_bank_type_pret SET nom = ?, description = ?, taux_interet = ?, duree_max_mois = ?, montant_min = ?, montant_max = ?, actif = ? WHERE id = ?");
        $stmt->execute([
            $data->nom,
            $data->description,
            $data->taux_interet,
            $data->duree_max_mois,
            $data->montant_min,
            $data->montant_max,
            $data->actif,
            $id
        ]);
        Flight::json(['message' => 'Type de prêt modifié']);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 400);
    }
});

Flight::route('DELETE /types-prets/@id', function($id) {
    try {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM s4_bank_type_pret WHERE id = ?");
        $stmt->execute([$id]);
        Flight::json(['message' => 'Type de prêt supprimé']);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Erreur: ' . $e->getMessage()], 400);
    }
});

// ========== ROUTES PRÊTS ==========
Flight::route('GET /prets', function() {
    $db = getDB();
    $stmt = $db->query("
        SELECT p.*,
               e.nom as etudiant_nom, e.prenom as etudiant_prenom, e.email as etudiant_email,
               tp.nom as type_pret_nom, tp.taux_interet as type_taux,
               ef.nom as etablissement_nom
        FROM s4_bank_pret p
        JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
        JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
        JOIN s4_bank_etablissement ef ON p.etablissement_id = ef.id
        ORDER BY p.date_demande DESC
    ");
    Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
});

Flight::route('GET /prets/@id', function($id) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT p.*,
               e.nom as etudiant_nom, e.prenom as etudiant_prenom, e.email as etudiant_email,
               tp.nom as type_pret_nom, tp.description as type_description,
               ef.nom as etablissement_nom
        FROM s4_bank_pret p
        JOIN s4_bank_etudiant e ON p.etudiant_id = e.id
        JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
        JOIN s4_bank_etablissement ef ON p.etablissement_id = ef.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        Flight::json($result);
    } else {
        Flight::json(['error' => 'Prêt non trouvé'], 404);
    }
});

// ========== ROUTES TRANSACTIONS ==========
Flight::route('GET /transactions', function() {
    $db = getDB();
    $stmt = $db->query("
        SELECT t.*, ef.nom as etablissement_nom
        FROM s4_bank_transaction t
        JOIN s4_bank_etablissement ef ON t.etablissement_id = ef.id
        ORDER BY t.date_transaction DESC
    ");
    Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
});

Flight::start();

