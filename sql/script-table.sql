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
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des fonds disponibles pour l'établissement
CREATE TABLE s4_bank_fond_disponible (
  id INT PRIMARY KEY AUTO_INCREMENT,
  montant DOUBLE NOT NULL,
  date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  description TEXT,
  etablissement_id INT,
  FOREIGN KEY (etablissement_id) REFERENCES s4_bank_etablissement(id)
);

-- Table des types de prêts
CREATE TABLE s4_bank_type_pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    taux_interet DECIMAL(5,2) NOT NULL, -- Taux en pourcentage (ex: 3.50 pour 3.5%)
    duree_max_mois INT NOT NULL, -- Durée maximale en mois
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
    montant_total DECIMAL(10,2) NOT NULL, -- Capital + intérêts
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

-- Table des transactions (mouvements de fonds)
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