-- Script de reset complet du système bancaire avec tables d'intérêts
    DROP DATABASE IF EXISTS examen_final_s4;
    CREATE DATABASE examen_final_s4;
USE examen_final_s4;

-- Supprime et recrée toutes les tables avec des données de test

-- Suppression des tables dans l'ordre inverse des dépendances
DROP VIEW IF EXISTS v_detail_interets;
DROP VIEW IF EXISTS v_interets_mensuels;
DROP TABLE IF EXISTS s4_bank_detail_interets;
DROP TABLE IF EXISTS s4_bank_interets_mensuels;
DROP TABLE IF EXISTS s4_bank_transaction;
DROP TABLE IF EXISTS s4_bank_remboursement;
DROP TABLE IF EXISTS s4_bank_pret;
DROP TABLE IF EXISTS s4_bank_type_pret;
DROP TABLE IF EXISTS s4_bank_etablissement;
DROP TABLE IF EXISTS s4_bank_etudiant;

-- ========================================
-- TABLES PRINCIPALES DU SYSTÈME BANCAIRE
-- ========================================

-- Table des étudiants/clients
CREATE TABLE s4_bank_etudiant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    age INT NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table de l'établissement financier (EF)
CREATE TABLE s4_bank_etablissement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(100),
    fonds_disponibles DECIMAL(15,2) DEFAULT 0.00,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des types de prêts
CREATE TABLE s4_bank_type_pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    taux_interet DECIMAL(5,2) NOT NULL,
    duree_max_mois INT NOT NULL,
    montant_min DECIMAL(10,2) DEFAULT 0.00,
    montant_max DECIMAL(10,2) DEFAULT NULL,
    actif BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des prêts
CREATE TABLE s4_bank_pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    etudiant_id INT NOT NULL,
    type_pret_id INT NOT NULL,
    etablissement_id INT NOT NULL,
    montant_demande DECIMAL(10,2) NOT NULL,
    montant_accorde DECIMAL(10,2) NOT NULL,
    taux_applique DECIMAL(5,2) NOT NULL,
    duree_mois INT NOT NULL,
    mensualite DECIMAL(10,2) NOT NULL,
    montant_total DECIMAL(10,2) NOT NULL,
    statut ENUM('en_attente', 'approuve', 'refuse', 'actif', 'rembourse', 'defaut') DEFAULT 'en_attente',
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_approbation TIMESTAMP NULL,
    date_debut TIMESTAMP NULL,
    date_fin_prevue TIMESTAMP NULL,
    FOREIGN KEY (etudiant_id) REFERENCES s4_bank_etudiant(id) ON DELETE CASCADE,
    FOREIGN KEY (type_pret_id) REFERENCES s4_bank_type_pret(id) ON DELETE RESTRICT,
    FOREIGN KEY (etablissement_id) REFERENCES s4_bank_etablissement(id) ON DELETE RESTRICT
);

-- Table des remboursements
CREATE TABLE s4_bank_remboursement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pret_id INT NOT NULL,
    numero_echeance INT NOT NULL,
    montant_prevu DECIMAL(10,2) NOT NULL,
    montant_paye DECIMAL(10,2) DEFAULT 0.00,
    date_echeance DATE NOT NULL,
    date_paiement TIMESTAMP NULL,
    statut ENUM('en_attente', 'paye', 'retard', 'defaut') DEFAULT 'en_attente',
    penalite DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (pret_id) REFERENCES s4_bank_pret(id) ON DELETE CASCADE
);

-- Table des transactions
CREATE TABLE s4_bank_transaction (
    id INT AUTO_INCREMENT PRIMARY KEY,
    etablissement_id INT NOT NULL,
    pret_id INT NULL,
    remboursement_id INT NULL,
    type_transaction ENUM('depot', 'pret_accorde', 'remboursement_recu', 'penalite') NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    solde_avant DECIMAL(15,2) NOT NULL,
    solde_apres DECIMAL(15,2) NOT NULL,
    description TEXT,
    date_transaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etablissement_id) REFERENCES s4_bank_etablissement(id) ON DELETE CASCADE,
    FOREIGN KEY (pret_id) REFERENCES s4_bank_pret(id) ON DELETE SET NULL,
    FOREIGN KEY (remboursement_id) REFERENCES s4_bank_remboursement(id) ON DELETE SET NULL
);

-- ========================================
-- TABLES DU SYSTÈME D'INTÉRÊTS
-- ========================================

-- Table pour stocker les intérêts gagnés par mois (résumé mensuel)
CREATE TABLE s4_bank_interets_mensuels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    etablissement_id INT NOT NULL,
    annee INT NOT NULL,
    mois INT NOT NULL,
    montant_interets DECIMAL(15,2) NOT NULL DEFAULT 0,
    nombre_prets_actifs INT NOT NULL DEFAULT 0,
    capital_total DECIMAL(15,2) NOT NULL DEFAULT 0,
    date_calcul TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etablissement_id) REFERENCES s4_bank_etablissement(id) ON DELETE CASCADE,
    UNIQUE KEY unique_periode (etablissement_id, annee, mois),
    INDEX idx_periode (annee, mois),
    INDEX idx_etablissement_periode (etablissement_id, annee, mois)
);

-- Table pour stocker le détail des intérêts par prêt
CREATE TABLE s4_bank_detail_interets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pret_id INT NOT NULL,
    etablissement_id INT NOT NULL,
    annee INT NOT NULL,
    mois INT NOT NULL,
    capital_restant DECIMAL(15,2) NOT NULL,
    taux_mensuel DECIMAL(8,5) NOT NULL,
    montant_interet DECIMAL(15,2) NOT NULL,
    date_calcul TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pret_id) REFERENCES s4_bank_pret(id) ON DELETE CASCADE,
    FOREIGN KEY (etablissement_id) REFERENCES s4_bank_etablissement(id) ON DELETE CASCADE,
    UNIQUE KEY unique_pret_periode (pret_id, annee, mois),
    INDEX idx_pret_periode (pret_id, annee, mois),
    INDEX idx_etablissement_periode (etablissement_id, annee, mois)
);

-- ========================================
-- VUES POUR LES INTÉRÊTS
-- ========================================

-- Vue pour faciliter les requêtes d'intérêts avec informations enrichies
CREATE VIEW v_interets_mensuels AS
SELECT
    im.id,
    im.etablissement_id,
    e.nom as etablissement_nom,
    im.annee,
    im.mois,
    im.montant_interets,
    im.nombre_prets_actifs,
    im.capital_total,
    im.date_calcul,
    CONCAT(im.annee, '-', LPAD(im.mois, 2, '0')) as periode,
    -- Calculs supplémentaires
    CASE
        WHEN im.nombre_prets_actifs > 0 THEN im.montant_interets / im.nombre_prets_actifs
        ELSE 0
    END as interet_moyen_par_pret,
    CASE
        WHEN im.capital_total > 0 THEN (im.montant_interets / im.capital_total) * 100
        ELSE 0
    END as taux_rendement_mensuel
FROM s4_bank_interets_mensuels im
JOIN s4_bank_etablissement e ON im.etablissement_id = e.id
ORDER BY im.annee DESC, im.mois DESC;

-- Vue pour les détails d'intérêts avec informations des prêts
CREATE VIEW v_detail_interets AS
SELECT
    di.id,
    di.pret_id,
    di.etablissement_id,
    e.nom as etablissement_nom,
    di.annee,
    di.mois,
    di.capital_restant,
    di.taux_mensuel,
    di.montant_interet,
    di.date_calcul,
    CONCAT(di.annee, '-', LPAD(di.mois, 2, '0')) as periode,
    -- Informations du prêt
    p.montant_accorde,
    p.taux_applique,
    p.duree_mois,
    p.statut as statut_pret,
    -- Informations de l'étudiant
    et.nom as etudiant_nom,
    et.prenom as etudiant_prenom,
    et.email as etudiant_email,
    -- Informations du type de prêt
    tp.nom as type_pret_nom
FROM s4_bank_detail_interets di
JOIN s4_bank_etablissement e ON di.etablissement_id = e.id
JOIN s4_bank_pret p ON di.pret_id = p.id
JOIN s4_bank_etudiant et ON p.etudiant_id = et.id
JOIN s4_bank_type_pret tp ON p.type_pret_id = tp.id
ORDER BY di.annee DESC, di.mois DESC, di.montant_interet DESC;

-- ========================================
-- RESET DES AUTO-INCREMENT
-- ========================================

ALTER TABLE s4_bank_etudiant AUTO_INCREMENT = 1;
ALTER TABLE s4_bank_etablissement AUTO_INCREMENT = 1;
ALTER TABLE s4_bank_type_pret AUTO_INCREMENT = 1;
ALTER TABLE s4_bank_pret AUTO_INCREMENT = 1;
ALTER TABLE s4_bank_remboursement AUTO_INCREMENT = 1;
ALTER TABLE s4_bank_transaction AUTO_INCREMENT = 1;
ALTER TABLE s4_bank_interets_mensuels AUTO_INCREMENT = 1;
ALTER TABLE s4_bank_detail_interets AUTO_INCREMENT = 1;

-- ========================================
-- INSERTION DES DONNÉES DE TEST
-- ========================================

-- Étudiants
INSERT INTO s4_bank_etudiant (nom, prenom, email, age, telephone, adresse) VALUES
('Dupont', 'Jean', 'jean.dupont@email.com', 22, '0612345678', '123 Rue de la Paix, 75001 Paris'),
('Martin', 'Alice', 'alice.martin@email.com', 20, '0623456789', '45 Avenue des Champs, 69001 Lyon'),
('Bernard', 'Paul', 'paul.bernard@email.com', 24, '0634567890', '78 Boulevard Saint-Michel, 33000 Bordeaux'),
('Durand', 'Marie', 'marie.durand@email.com', 21, '0645678901', '12 Place Bellecour, 69002 Lyon'),
('Moreau', 'Pierre', 'pierre.moreau@email.com', 23, '0656789012', '89 Rue de Rivoli, 75004 Paris');

-- Établissement financier avec fonds initial
INSERT INTO s4_bank_etablissement (nom, adresse, telephone, email, fonds_disponibles) VALUES
('Banque Étudiante de France', '100 Avenue de la République, 75011 Paris', '0142000000', 'contact@bef.fr', 5000000.00);

-- Types de prêts avec différents taux
INSERT INTO s4_bank_type_pret (nom, description, taux_interet, duree_max_mois, montant_min, montant_max) VALUES
('Prêt Étudiant Standard', 'Prêt pour financer les études supérieures', 2.50, 120, 1000.00, 50000.00),
('Prêt Équipement Informatique', 'Prêt pour achat d\'ordinateur et matériel informatique', 3.20, 36, 500.00, 5000.00),
('Prêt Logement Étudiant', 'Prêt pour caution et premier loyer', 1.80, 60, 2000.00, 15000.00),
('Prêt Mobilité', 'Prêt pour études à l\'étranger ou transport', 4.10, 48, 1500.00, 20000.00),
('Prêt d\'Urgence', 'Prêt rapide pour situations d\'urgence', 6.50, 12, 200.00, 2000.00);

-- Transaction initiale de dépôt de fonds
INSERT INTO s4_bank_transaction (etablissement_id, type_transaction, montant, solde_avant, solde_apres, description) VALUES
(1, 'depot', 5000000.00, 0.00, 5000000.00, 'Dépôt initial de fonds dans l\'établissement');

-- Quelques prêts d'exemple
INSERT INTO s4_bank_pret (etudiant_id, type_pret_id, etablissement_id, montant_demande, montant_accorde, taux_applique, duree_mois, mensualite, montant_total, statut, date_approbation, date_debut, date_fin_prevue) VALUES
(1, 1, 1, 15000.00, 15000.00, 2.50, 60, 266.93, 16015.80, 'actif', NOW(), NOW(), DATE_ADD(NOW(), INTERVAL 60 MONTH)),
(2, 2, 1, 2000.00, 2000.00, 3.20, 24, 87.41, 2097.84, 'actif', NOW(), NOW(), DATE_ADD(NOW(), INTERVAL 24 MONTH)),
(3, 3, 1, 8000.00, 8000.00, 1.80, 48, 173.33, 8319.84, 'approuve', NOW(), NOW(), DATE_ADD(NOW(), INTERVAL 48 MONTH));

-- Transactions des prêts accordés
INSERT INTO s4_bank_transaction (etablissement_id, pret_id, type_transaction, montant, solde_avant, solde_apres, description) VALUES
(1, 1, 'pret_accorde', 15000.00, 5000000.00, 4985000.00, 'Prêt accordé à Jean Dupont'),
(1, 2, 'pret_accorde', 2000.00, 4985000.00, 4983000.00, 'Prêt accordé à Alice Martin'),
(1, 3, 'pret_accorde', 8000.00, 4983000.00, 4975000.00, 'Prêt accordé à Paul Bernard');

-- Mise à jour des fonds de l'établissement
UPDATE s4_bank_etablissement SET fonds_disponibles = 4975000.00 WHERE id = 1;

-- Échéanciers pour les prêts actifs (premières échéances)
INSERT INTO s4_bank_remboursement (pret_id, numero_echeance, montant_prevu, date_echeance, statut) VALUES
-- Pour le prêt de Jean (ID 1)
(1, 1, 266.93, DATE_ADD(NOW(), INTERVAL 1 MONTH), 'en_attente'),
(1, 2, 266.93, DATE_ADD(NOW(), INTERVAL 2 MONTH), 'en_attente'),
(1, 3, 266.93, DATE_ADD(NOW(), INTERVAL 3 MONTH), 'en_attente'),
-- Pour le prêt d'Alice (ID 2)
(2, 1, 87.41, DATE_ADD(NOW(), INTERVAL 1 MONTH), 'en_attente'),
(2, 2, 87.41, DATE_ADD(NOW(), INTERVAL 2 MONTH), 'en_attente'),
(2, 3, 87.41, DATE_ADD(NOW(), INTERVAL 3 MONTH), 'en_attente'),
-- Pour le prêt de Paul (ID 3)
(3, 1, 173.33, DATE_ADD(NOW(), INTERVAL 1 MONTH), 'en_attente'),
(3, 2, 173.33, DATE_ADD(NOW(), INTERVAL 2 MONTH), 'en_attente'),
(3, 3, 173.33, DATE_ADD(NOW(), INTERVAL 3 MONTH), 'en_attente');

-- ========================================
-- DONNÉES DE TEST POUR LES INTÉRÊTS
-- ========================================

-- Insertion de données de test pour les intérêts mensuels (mois actuel)
INSERT INTO s4_bank_interets_mensuels (etablissement_id, annee, mois, montant_interets, nombre_prets_actifs, capital_total)
SELECT
    1 as etablissement_id,
    YEAR(CURDATE()) as annee,
    MONTH(CURDATE()) as mois,
    ROUND(SUM(p.montant_accorde * (p.taux_applique / 100 / 12)), 2) as montant_interets,
    COUNT(p.id) as nombre_prets_actifs,
    SUM(p.montant_accorde) as capital_total
FROM s4_bank_pret p
WHERE p.statut IN ('actif', 'approuve')
AND p.etablissement_id = 1;

-- Insertion des détails d'intérêts par prêt pour le mois actuel
INSERT INTO s4_bank_detail_interets (pret_id, etablissement_id, annee, mois, capital_restant, taux_mensuel, montant_interet)
SELECT
    p.id as pret_id,
    p.etablissement_id,
    YEAR(CURDATE()) as annee,
    MONTH(CURDATE()) as mois,
    p.montant_accorde as capital_restant,
    p.taux_applique / 100 / 12 as taux_mensuel,
    ROUND(p.montant_accorde * (p.taux_applique / 100 / 12), 2) as montant_interet
FROM s4_bank_pret p
WHERE p.statut IN ('actif', 'approuve')
AND p.etablissement_id = 1;

-- DONNÉES SUPPLÉMENTAIRES POUR LE GRAPHIQUE (6 mois précédents)
-- Mois -1
INSERT INTO s4_bank_interets_mensuels (etablissement_id, annee, mois, montant_interets, nombre_prets_actifs, capital_total, date_calcul) VALUES
(1, YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), 52.50, 3, 25000.00, DATE_SUB(CURDATE(), INTERVAL 1 MONTH));

-- Mois -2
INSERT INTO s4_bank_interets_mensuels (etablissement_id, annee, mois, montant_interets, nombre_prets_actifs, capital_total, date_calcul) VALUES
(1, YEAR(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)), 48.75, 3, 25000.00, DATE_SUB(CURDATE(), INTERVAL 2 MONTH));

-- Mois -3
INSERT INTO s4_bank_interets_mensuels (etablissement_id, annee, mois, montant_interets, nombre_prets_actifs, capital_total, date_calcul) VALUES
(1, YEAR(DATE_SUB(CURDATE(), INTERVAL 3 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 3 MONTH)), 45.20, 2, 17000.00, DATE_SUB(CURDATE(), INTERVAL 3 MONTH));

-- Mois -4
INSERT INTO s4_bank_interets_mensuels (etablissement_id, annee, mois, montant_interets, nombre_prets_actifs, capital_total, date_calcul) VALUES
(1, YEAR(DATE_SUB(CURDATE(), INTERVAL 4 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 4 MONTH)), 35.60, 2, 17000.00, DATE_SUB(CURDATE(), INTERVAL 4 MONTH));

-- Mois -5
INSERT INTO s4_bank_interets_mensuels (etablissement_id, annee, mois, montant_interets, nombre_prets_actifs, capital_total, date_calcul) VALUES
(1, YEAR(DATE_SUB(CURDATE(), INTERVAL 5 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 5 MONTH)), 31.25, 1, 15000.00, DATE_SUB(CURDATE(), INTERVAL 5 MONTH));

-- Mois -6
INSERT INTO s4_bank_interets_mensuels (etablissement_id, annee, mois, montant_interets, nombre_prets_actifs, capital_total, date_calcul) VALUES
(1, YEAR(DATE_SUB(CURDATE(), INTERVAL 6 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 6 MONTH)), 31.25, 1, 15000.00, DATE_SUB(CURDATE(), INTERVAL 6 MONTH));

-- DÉTAILS POUR LES MOIS PRÉCÉDENTS (pour enrichir les données)
-- Mois -1 : Détails par prêt
INSERT INTO s4_bank_detail_interets (pret_id, etablissement_id, annee, mois, capital_restant, taux_mensuel, montant_interet, date_calcul) VALUES
(1, 1, YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), 14800.00, 0.00208, 30.83, DATE_SUB(CURDATE(), INTERVAL 1 MONTH)),
(2, 1, YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), 1920.00, 0.00267, 5.12, DATE_SUB(CURDATE(), INTERVAL 1 MONTH)),
(3, 1, YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)), 8000.00, 0.00150, 12.00, DATE_SUB(CURDATE(), INTERVAL 1 MONTH));

-- Mois -2 : Détails par prêt
INSERT INTO s4_bank_detail_interets (pret_id, etablissement_id, annee, mois, capital_restant, taux_mensuel, montant_interet, date_calcul) VALUES
(1, 1, YEAR(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)), 14600.00, 0.00208, 30.42, DATE_SUB(CURDATE(), INTERVAL 2 MONTH)),
(2, 1, YEAR(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)), 1840.00, 0.00267, 4.91, DATE_SUB(CURDATE(), INTERVAL 2 MONTH)),
(3, 1, YEAR(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)), 8000.00, 0.00150, 12.00, DATE_SUB(CURDATE(), INTERVAL 2 MONTH));

-- Mois -3 : Seulement 2 prêts
INSERT INTO s4_bank_detail_interets (pret_id, etablissement_id, annee, mois, capital_restant, taux_mensuel, montant_interet, date_calcul) VALUES
(1, 1, YEAR(DATE_SUB(CURDATE(), INTERVAL 3 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 3 MONTH)), 14400.00, 0.00208, 30.00, DATE_SUB(CURDATE(), INTERVAL 3 MONTH)),
(2, 1, YEAR(DATE_SUB(CURDATE(), INTERVAL 3 MONTH)), MONTH(DATE_SUB(CURDATE(), INTERVAL 3 MONTH)), 1760.00, 0.00267, 4.70, DATE_SUB(CURDATE(), INTERVAL 3 MONTH));

-- DONNÉES FUTURES POUR PROJECTION (2 mois suivants)
-- Mois +1
INSERT INTO s4_bank_interets_mensuels (etablissement_id, annee, mois, montant_interets, nombre_prets_actifs, capital_total, date_calcul) VALUES
(1, YEAR(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)), MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)), 58.25, 3, 25000.00, CURDATE());

-- Mois +2
INSERT INTO s4_bank_interets_mensuels (etablissement_id, annee, mois, montant_interets, nombre_prets_actifs, capital_total, date_calcul) VALUES
(1, YEAR(DATE_ADD(CURDATE(), INTERVAL 2 MONTH)), MONTH(DATE_ADD(CURDATE(), INTERVAL 2 MONTH)), 62.10, 4, 30000.00, CURDATE());
