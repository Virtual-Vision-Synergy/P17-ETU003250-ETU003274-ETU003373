# API de Gestion des Prêts Clients

## Description
Ce système fournit une API REST complète pour la gestion des prêts clients avec le même traitement de données que celui utilisé pour les étudiants. Il permet de gérer les prêts, les remboursements, les types de prêts et les établissements financiers.

## Endpoints disponibles

### 1. Gestion des Prêts

#### GET /prets
Récupère la liste de tous les prêts avec les informations des clients, types de prêts et établissements.
```json
Response: [
  {
    "id": 1,
    "etudiant_id": 1,
    "nom": "RAKOTO",
    "prenom": "Jean",
    "email": "jean.rakoto@email.com",
    "type_pret_nom": "Prêt Étudiant Standard",
    "etablissement_nom": "Banque Centrale de Madagascar",
    "montant_demande": "500000.00",
    "montant_accorde": "500000.00",
    "taux_applique": "8.50",
    "duree_mois": 24,
    "mensualite": "22916.67",
    "montant_total": "550000.00",
    "statut": "actif"
  }
]
```

#### GET /prets/{id}
Récupère les détails d'un prêt spécifique.

#### POST /prets
Crée un nouveau prêt avec calcul automatique de la mensualité.
```json
Request body:
{
  "etudiant_id": 1,
  "type_pret_id": 1,
  "etablissement_id": 1,
  "montant_demande": 500000,
  "montant_accorde": 500000,
  "taux_applique": 8.5,
  "duree_mois": 24,
  "statut": "en_attente"
}
```

#### PUT /prets/{id}
Met à jour un prêt existant avec recalcul automatique si nécessaire.

#### DELETE /prets/{id}
Supprime un prêt.

### 2. Gestion des Remboursements

#### GET /prets/{id}/remboursements
Récupère toutes les échéances de remboursement d'un prêt.

#### POST /prets/{id}/remboursements
Ajoute une nouvelle échéance de remboursement.
```json
Request body:
{
  "numero_echeance": 1,
  "montant_prevu": 22916.67,
  "date_echeance": "2025-08-07"
}
```

#### PUT /remboursements/{id}/payer
Marque un remboursement comme payé.
```json
Request body:
{
  "montant_paye": 22916.67
}
```

### 3. Gestion des Types de Prêts

#### GET /types-prets
Récupère tous les types de prêts actifs.

#### POST /types-prets
Crée un nouveau type de prêt.
```json
Request body:
{
  "nom": "Prêt Étudiant Standard",
  "description": "Prêt pour frais de scolarité",
  "taux_interet": 8.5,
  "duree_max_mois": 60,
  "montant_min": 100000,
  "montant_max": 2000000
}
```

### 4. Gestion des Établissements

#### GET /etablissements
Récupère tous les établissements financiers.

#### POST /etablissements
Crée un nouvel établissement.
```json
Request body:
{
  "nom": "Banque Centrale de Madagascar",
  "adresse": "Antananarivo",
  "telephone": "0202020202",
  "email": "bcm@banque.mg",
  "fonds_disponibles": 5000000
}
```

### 5. Gestion des Étudiants/Clients (existant)

#### GET /etudiants
Récupère tous les étudiants/clients.

#### POST /etudiants
Crée un nouveau client.

#### PUT /etudiants/{id}
Met à jour un client.

#### DELETE /etudiants/{id}
Supprime un client.

## Fonctionnalités Spéciales

### Calcul Automatique des Mensualités
Le système calcule automatiquement :
- La mensualité en fonction du montant, taux et durée
- Le montant total à rembourser
- Les intérêts totaux

### Gestion des Statuts
- `en_attente` : Demande en cours d'examen
- `approuve` : Prêt approuvé mais pas encore actif
- `refuse` : Demande refusée
- `actif` : Prêt en cours de remboursement
- `rembourse` : Prêt entièrement remboursé
- `defaut` : Prêt en défaut de paiement

### Gestion des Échéances
- Suivi automatique des dates d'échéance
- Statuts des remboursements (en_attente, paye, retard, defaut)
- Calcul des pénalités de retard

## Configuration de la Base de Données

Exécutez le script `sql/script-reset.sql` pour :
- Créer toutes les tables nécessaires
- Insérer des données de test
- Configurer les relations entre tables

## Utilisation

1. Démarrez votre serveur Apache/PHP
2. Importez la base de données avec le script SQL
3. Accédez aux endpoints via : `http://localhost/P17-ETU003250-ETU003274-ETU003373/ws/`

Exemple : `GET http://localhost/P17-ETU003250-ETU003274-ETU003373/ws/prets`

## Traitement des Données

Le système utilise le même pattern de traitement que pour les étudiants :
- Validation des données d'entrée
- Gestion des erreurs avec messages explicites
- Retour JSON standardisé
- Gestion des relations entre entités
- Calculs automatiques pour les aspects financiers
