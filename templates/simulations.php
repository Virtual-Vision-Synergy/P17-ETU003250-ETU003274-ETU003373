<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système Bancaire Étudiant - Simulation et Comparaison de Prêts</title>

    <!-- Bootstrap 5 CSS -->
    <link href="../assets/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="../assets/fontawesome-free-6.7.2-web/css/all.min.css">

    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/simulations.jpeg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
        }

        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 500;
            border-radius: 10px 10px 0 0;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }

        .simulation-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .comparison-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .summary-item {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
        }

        .pagination-controls {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .loan-details {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .loan-details h5 {
            color: #495057;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .comparison-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .comparison-table th {
            background: #007bff;
            color: white;
            text-align: center;
            font-weight: 600;
        }

        .comparison-table td {
            text-align: center;
            vertical-align: middle;
            padding: 1rem;
        }

        .better-value {
            background-color: #d4edda !important;
            color: #155724;
            font-weight: bold;
        }

        .worse-value {
            background-color: #f8d7da !important;
            color: #721c24;
        }

        .metric-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1rem;
        }

        .metric-card:hover {
            transform: translateY(-5px);
        }

        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }

        .metric-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .recommendation-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .tab-content {
            margin-top: 2rem;
        }
    </style>
</head>

<body>
<!-- Navigation -->
<?php include '../includes/header.php'; ?>

<!-- Section Hero -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 mb-4">
            <i class="fas fa-calculator me-3"></i>
            Simulation et Comparaison de Prêts
        </h1>
        <p class="lead">
            Simulez vos prêts étudiants et comparez les différentes options pour prendre la meilleure décision financière
        </p>
    </div>
</section>

<!-- Contenu Principal -->
<div class="container mt-5">
    <!-- Onglets de navigation -->
    <ul class="nav nav-tabs" id="mainTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="simulation-tab" data-bs-toggle="tab" data-bs-target="#simulation" type="button" role="tab" aria-controls="simulation" aria-selected="true">
                <i class="fas fa-calculator me-2"></i>Simulation de Prêt
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="comparison-tab" data-bs-toggle="tab" data-bs-target="#comparison" type="button" role="tab" aria-controls="comparison" aria-selected="false">
                <i class="fas fa-balance-scale me-2"></i>Comparaison de Prêts
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="saved-tab" data-bs-toggle="tab" data-bs-target="#saved-simulations" type="button" role="tab" aria-controls="saved-simulations" aria-selected="false">
                <i class="fas fa-save me-2"></i>Simulations Sauvegardées
            </button>
        </li>
    </ul>

    <!-- Contenu des onglets -->
    <div class="tab-content" id="mainTabsContent">
        <!-- Onglet Simulation -->
        <div class="tab-pane fade show active" id="simulation" role="tabpanel" aria-labelledby="simulation-tab">
            <!-- Simulation -->
            <div class="simulation-card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-calculator me-2"></i>Simulation de Prêt</h4>
                </div>
                <div class="card-body">
                    <!-- Type de simulation -->
                    <div class="mb-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="simulation_type" id="simulation_manuelle" value="manuelle" checked>
                            <label class="form-check-label" for="simulation_manuelle">
                                <i class="fas fa-keyboard me-2"></i>Simulation Manuelle
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="simulation_type" id="simulation_existante" value="existante">
                            <label class="form-check-label" for="simulation_existante">
                                <i class="fas fa-database me-2"></i>Simuler un Prêt Existant
                            </label>
                        </div>
                    </div>

                    <!-- Simulation manuelle -->
                    <div id="simulation_manuelle_form">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sim_capital" class="form-label">Capital (€)</label>
                                    <input type="number" class="form-control" id="sim_capital" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sim_taux" class="form-label">Taux Annuel (%)</label>
                                    <input type="number" class="form-control" id="sim_taux" step="0.01" min="0" max="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sim_duree" class="form-label">Durée (mois)</label>
                                    <input type="number" class="form-control" id="sim_duree" min="1">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-light" onclick="simulateManualLoan()">
                            <i class="fas fa-play me-2"></i>Lancer la Simulation
                        </button>
                    </div>

                    <!-- Simulation prêt existant -->
                    <div id="simulation_existante_form" style="display: none;">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="select_pret_existant" class="form-label">Prêt à simuler</label>
                                    <select class="form-select" id="select_pret_existant">
                                        <option value="">Chargement...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-light d-block w-100" onclick="simulateExistingLoan()">
                                        <i class="fas fa-play me-2"></i>Simuler ce Prêt
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résultats de simulation -->
            <div id="simulation-summary" class="card" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Résumé de la Simulation</h5>
                    <div>
                        <button type="button" class="btn btn-success btn-sm" onclick="saveCurrentSimulation()">
                            <i class="fas fa-save me-2"></i>Sauvegarder cette simulation
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="summary-item">
                                <div class="summary-value" id="summary-annuite">-</div>
                                <small class="text-muted">Annuité Mensuelle</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item">
                                <div class="summary-value" id="summary-total">-</div>
                                <small class="text-muted">Montant Total</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item">
                                <div class="summary-value" id="summary-cout">-</div>
                                <small class="text-muted">Coût du Crédit</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item">
                                <div class="summary-value" id="summary-duree">-</div>
                                <small class="text-muted">Durée (mois)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau d'amortissement avec pagination -->
            <div id="amortization-table" class="card mt-4" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Tableau d'Amortissement</h5>
                    <div class="pagination-info">
                        <span id="pagination-info" class="text-muted small"></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-dark">
                            <tr>
                                <th>Échéance</th>
                                <th>Capital Restant Début</th>
                                <th>Annuité</th>
                                <th>Intérêts</th>
                                <th>Capital Remboursé</th>
                                <th>Capital Restant Fin</th>
                            </tr>
                            </thead>
                            <tbody id="amortization-body">
                            <!-- Généré par JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Contrôles de pagination -->
                    <div class="pagination-controls" id="pagination-controls" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-outline-primary btn-sm" id="btn-first-page" onclick="goToPage(1)">
                                    <i class="fas fa-angle-double-left"></i> Première
                                </button>
                                <button class="btn btn-outline-primary btn-sm" id="btn-prev-page" onclick="goToPreviousPage()">
                                    <i class="fas fa-angle-left"></i> Précédent
                                </button>
                            </div>

                            <div class="btn-group" role="group" id="page-numbers">
                                <!-- Numéros de page générés dynamiquement -->
                            </div>

                            <div>
                                <button class="btn btn-outline-primary btn-sm" id="btn-next-page" onclick="goToNextPage()">
                                    Suivant <i class="fas fa-angle-right"></i>
                                </button>
                                <button class="btn btn-outline-primary btn-sm" id="btn-last-page" onclick="goToLastPage()">
                                    Dernière <i class="fas fa-angle-double-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Comparaison -->
        <div class="tab-pane fade" id="comparison" role="tabpanel" aria-labelledby="comparison-tab">
            <!-- Formulaire de saisie manuelle -->
            <div class="form-section">
                <h3 class="mb-4">
                    <i class="fas fa-edit me-2"></i>
                    Saisir les données des prêts à comparer
                </h3>
                <form id="form-comparaison">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Prêt 1</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="pret1_nom" class="form-label">Nom du prêt</label>
                                        <input type="text" class="form-control" id="pret1_nom" placeholder="Ex: Prêt étudiant A" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pret1_montant" class="form-label">Montant (€)</label>
                                        <input type="number" class="form-control" id="pret1_montant" step="0.01" min="0" placeholder="Ex: 10000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pret1_taux" class="form-label">Taux d'intérêt (%)</label>
                                        <input type="number" class="form-control" id="pret1_taux" step="0.01" min="0" max="100" placeholder="Ex: 3.5" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pret1_duree" class="form-label">Durée (mois)</label>
                                        <input type="number" class="form-control" id="pret1_duree" min="1" placeholder="Ex: 24" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Prêt 2</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="pret2_nom" class="form-label">Nom du prêt</label>
                                        <input type="text" class="form-control" id="pret2_nom" placeholder="Ex: Prêt étudiant B" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pret2_montant" class="form-label">Montant (€)</label>
                                        <input type="number" class="form-control" id="pret2_montant" step="0.01" min="0" placeholder="Ex: 15000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pret2_taux" class="form-label">Taux d'intérêt (%)</label>
                                        <input type="number" class="form-control" id="pret2_taux" step="0.01" min="0" max="100" placeholder="Ex: 4.0" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pret2_duree" class="form-label">Durée (mois)</label>
                                        <input type="number" class="form-control" id="pret2_duree" min="1" placeholder="Ex: 36" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary btn-lg" onclick="compareManualLoans()">
                            <i class="fas fa-balance-scale me-2"></i>Comparer les Prêts
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg ms-2" onclick="resetForm()">
                            <i class="fas fa-undo me-2"></i>Réinitialiser
                        </button>
                    </div>
                </form>
            </div>

            <!-- Résultats de la comparaison -->
            <div id="comparison-results" style="display: none;">
                <!-- Détails des prêts -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="loan-details">
                            <h5><i class="fas fa-file-alt me-2"></i>Détails du Prêt 1</h5>
                            <div id="loan1-details">
                                <!-- Détails du prêt 1 -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="loan-details">
                            <h5><i class="fas fa-file-alt me-2"></i>Détails du Prêt 2</h5>
                            <div id="loan2-details">
                                <!-- Détails du prêt 2 -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métriques clés -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value" id="metric-difference">0%</div>
                            <div class="metric-label">Différence de Coût</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value" id="metric-savings">0€</div>
                            <div class="metric-label">Économies Potentielles</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value" id="metric-duration">0 mois</div>
                            <div class="metric-label">Différence de Durée</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value" id="metric-monthly">0€</div>
                            <div class="metric-label">Différence Mensuelle</div>
                        </div>
                    </div>
                </div>

                <!-- Tableau de comparaison -->
                <div class="table-responsive comparison-table">
                    <table class="table table-striped mb-0">
                        <thead>
                        <tr>
                            <th>Critère</th>
                            <th>Prêt 1</th>
                            <th>Prêt 2</th>
                            <th>Avantage</th>
                        </tr>
                        </thead>
                        <tbody id="comparison-table-body">
                        <!-- Résultats de comparaison -->
                        </tbody>
                    </table>
                </div>

                <!-- Recommandation -->
                <div class="recommendation-card">
                    <h4><i class="fas fa-lightbulb me-2"></i>Recommandation</h4>
                    <div id="recommendation-text">
                        <!-- Texte de recommandation -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Simulations Sauvegardées -->
        <div class="tab-pane fade" id="saved-simulations" role="tabpanel" aria-labelledby="saved-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-save me-2"></i>Simulations Sauvegardées</h5>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="loadSavedSimulations()">
                            <i class="fas fa-sync-alt me-2"></i>Actualiser la liste
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="saved-simulations-list" class="mb-4">
                        <p class="text-muted">Chargement des simulations sauvegardées...</p>
                    </div>
                </div>
            </div>

            <!-- Modal pour afficher les détails d'une simulation -->
            <div class="modal fade" id="simulationDetailsModal" tabindex="-1" aria-labelledby="simulationDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="simulationDetailsModalLabel">Détails de la simulation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Résumé de la simulation -->


                            <!-- Tableau d'amortissement -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Tableau d'Amortissement</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Échéance</th>
                                                    <th>Capital Restant Début</th>
                                                    <th>Annuité</th>
                                                    <th>Intérêts</th>
                                                    <th>Capital Remboursé</th>
                                                    <th>Capital Restant Fin</th>
                                                </tr>
                                            </thead>
                                            <tbody id="modal-amortization-body">
                                                <!-- Généré par JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<!-- Bootstrap 5 JS -->
   <script src="../assets/bootstrap.js"></script>
<script>
    const API_BASE = '../ws/';

    // Variables de pagination pour le tableau d'amortissement
    let amortizationData = [];
    let currentPage = 1;
    const itemsPerPage = 10;

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        loadExistingLoans();
        loadSavedSimulations();
        // Gestionnaire pour les types de simulation
        document.querySelectorAll('input[name="simulation_type"]').forEach(radio => {
            radio.addEventListener('change', toggleSimulationType);
        });
    });

    function formatCurrency(amount) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(amount);
    }

    function toggleSimulationType() {
        const manuelleForm = document.getElementById('simulation_manuelle_form');
        const existanteForm = document.getElementById('simulation_existante_form');

        if (document.getElementById('simulation_manuelle').checked) {
            manuelleForm.style.display = 'block';
            existanteForm.style.display = 'none';
        } else {
            manuelleForm.style.display = 'none';
            existanteForm.style.display = 'block';
        }
    }

    async function simulateManualLoan() {
        const capital = parseFloat(document.getElementById('sim_capital').value);
        const taux = parseFloat(document.getElementById('sim_taux').value);
        const duree = parseInt(document.getElementById('sim_duree').value);

        if (!capital || !taux || !duree) {
            alert('Veuillez remplir tous les champs');
            return;
        }

        try {
            const response = await fetch(API_BASE + 'remboursements/simuler', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    capital: capital,
                    taux_annuel: taux,
                    duree_mois: duree
                })
            });

            const result = await response.json();

            if (result.success) {
                displaySimulationResults(result.data);
            } else {
                throw new Error(result.message || 'Erreur de simulation');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la simulation: ' + error.message);
        }
    }

    async function loadExistingLoans() {
        try {
            const response = await fetch(API_BASE + 'remboursements/prets-valides');
            const result = await response.json();

            const select = document.getElementById('select_pret_existant');
            select.innerHTML = '<option value="">Sélectionner un prêt...</option>';

            if (result.success && result.data.length > 0) {
                result.data.forEach(pret => {
                    const option = document.createElement('option');
                    option.value = pret.id;
                    option.innerHTML = `${pret.etudiant_prenom} ${pret.etudiant_nom} - ${pret.type_pret} - ${formatCurrency(pret.montant_accorde)}`;
                    select.appendChild(option);
                });
            } else {
                select.innerHTML = '<option value="">Aucun prêt actif trouvé</option>';
            }
        } catch (error) {
            console.error('Erreur:', error);
            document.getElementById('select_pret_existant').innerHTML = '<option value="">Erreur de chargement</option>';
        }
    }

    async function simulateExistingLoan() {
        const loanId = document.getElementById('select_pret_existant').value;

        if (!loanId) {
            alert('Veuillez sélectionner un prêt');
            return;
        }

        try {
            const response = await fetch(API_BASE + 'remboursements/simuler-existant', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ pret_id: loanId })
            });

            const result = await response.json();

            if (result.success) {
                document.getElementById('sim_capital').value = result.data.simulation.capital;
                document.getElementById('sim_taux').value = result.data.simulation.taux_annuel;
                document.getElementById('sim_duree').value = result.data.simulation.duree_mois;

                document.getElementById('simulation_manuelle').checked = true;
                toggleSimulationType();

                displaySimulationResults(result.data);
            } else {
                throw new Error(result.message || 'Erreur de simulation');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la simulation: ' + error.message);
        }
    }

    // Variable pour stocker la simulation actuelle
    let currentSimulationData = null;

    function displaySimulationResults(data) {
        // Stockage des données de la simulation actuelle pour la sauvegarde
        currentSimulationData = data;

        // Afficher le résumé
        document.getElementById('summary-annuite').textContent = formatCurrency(data.simulation.annuite);
        document.getElementById('summary-total').textContent = formatCurrency(data.simulation.montant_total);
        document.getElementById('summary-cout').textContent = formatCurrency(data.simulation.cout_credit);
        document.getElementById('summary-duree').textContent = data.simulation.duree_mois;

        // Stocker les données d'amortissement pour la pagination
        amortizationData = data.tableau_amortissement;
        currentPage = 1;

        // Afficher le tableau d'amortissement avec pagination
        displayAmortizationPage();

        // Afficher les sections
        document.getElementById('simulation-summary').style.display = 'block';
        document.getElementById('amortization-table').style.display = 'block';

        // Afficher les contrôles de pagination si nécessaire
        if (amortizationData.length > itemsPerPage) {
            document.getElementById('pagination-controls').style.display = 'block';
            setupPagination();
        } else {
            document.getElementById('pagination-controls').style.display = 'none';
        }
    }

    // Fonctions de pagination pour le tableau d'amortissement
    function displayAmortizationPage() {
        const tbody = document.getElementById('amortization-body');
        tbody.innerHTML = '';

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, amortizationData.length);
        const pageData = amortizationData.slice(startIndex, endIndex);

        pageData.forEach(echeance => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${echeance.numero_echeance}</strong></td>
                <td>${formatCurrency(echeance.capital_restant_debut)}</td>
                <td class="text-primary fw-bold">${formatCurrency(echeance.annuite)}</td>
                <td class="text-warning">${formatCurrency(echeance.interets)}</td>
                <td class="text-success">${formatCurrency(echeance.capital_rembourse)}</td>
                <td>${formatCurrency(echeance.capital_restant_fin)}</td>
            `;
            tbody.appendChild(row);
        });

        // Mettre à jour l'information de pagination
        updatePaginationInfo();
    }

    function updatePaginationInfo() {
        const totalPages = Math.ceil(amortizationData.length / itemsPerPage);
        const startItem = (currentPage - 1) * itemsPerPage + 1;
        const endItem = Math.min(currentPage * itemsPerPage, amortizationData.length);

        document.getElementById('pagination-info').textContent =
            `Affichage ${startItem}-${endItem} sur ${amortizationData.length} échéances (Page ${currentPage}/${totalPages})`;
    }

    function setupPagination() {
        const totalPages = Math.ceil(amortizationData.length / itemsPerPage);
        const pageNumbersContainer = document.getElementById('page-numbers');
        pageNumbersContainer.innerHTML = '';

        // Calculer la plage de pages à afficher
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        // Assurer qu'on affiche au moins 5 pages si possible
        if (endPage - startPage < 4) {
            if (startPage === 1) {
                endPage = Math.min(totalPages, startPage + 4);
            } else {
                startPage = Math.max(1, endPage - 4);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const button = document.createElement('button');
            button.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}`;
            button.textContent = i;
            button.onclick = () => goToPage(i);
            pageNumbersContainer.appendChild(button);
        }

        // Mettre à jour l'état des boutons de navigation
        document.getElementById('btn-first-page').disabled = currentPage === 1;
        document.getElementById('btn-prev-page').disabled = currentPage === 1;
        document.getElementById('btn-next-page').disabled = currentPage === totalPages;
        document.getElementById('btn-last-page').disabled = currentPage === totalPages;
    }

    function goToPage(page) {
        const totalPages = Math.ceil(amortizationData.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;

        currentPage = page;
        displayAmortizationPage();
        setupPagination();
    }

    function goToPreviousPage() {
        if (currentPage > 1) {
            goToPage(currentPage - 1);
        }
    }

    function goToNextPage() {
        const totalPages = Math.ceil(amortizationData.length / itemsPerPage);
        if (currentPage < totalPages) {
            goToPage(currentPage + 1);
        }
    }

    function goToLastPage() {
        const totalPages = Math.ceil(amortizationData.length / itemsPerPage);
        goToPage(totalPages);
    }
</script>
<script>
    let loan1Data = null;
    let loan2Data = null;

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        // loadLoansForComparison();
    });

    function formatCurrency(amount) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(amount);
    }

    function formatPercentage(value) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'percent',
            minimumFractionDigits: 2
        }).format(value / 100);
    }

    async function loadLoansForComparison() {
        try {
            const response = await fetch(API_BASE + 'prets');
            const result = await response.json();

            const select1 = document.getElementById('select_pret_1');
            const select2 = document.getElementById('select_pret_2');

            select1.innerHTML = '<option value="">Sélectionner un prêt...</option>';
            select2.innerHTML = '<option value="">Sélectionner un prêt...</option>';

            if (result.success && result.data.length > 0) {
                result.data.forEach(pret => {
                    const option = document.createElement('option');
                    option.value = pret.id;
                    option.innerHTML = `${pret.etudiant_prenom} ${pret.etudiant_nom} - ${pret.type_pret} - ${formatCurrency(pret.montant_accorde)}`;
                    option.dataset.pret = JSON.stringify(pret);

                    select1.appendChild(option.cloneNode(true));
                    select2.appendChild(option);
                });
            } else {
                select1.innerHTML = '<option value="">Aucun prêt trouvé</option>';
                select2.innerHTML = '<option value="">Aucun prêt trouvé</option>';
            }
        } catch (error) {
            console.error('Erreur:', error);
            document.getElementById('select_pret_1').innerHTML = '<option value="">Erreur de chargement</option>';
            document.getElementById('select_pret_2').innerHTML = '<option value="">Erreur de chargement</option>';
        }
    }

    async function compareLoans() {
        const pret1Id = document.getElementById('select_pret_1').value;
        const pret2Id = document.getElementById('select_pret_2').value;

        if (!pret1Id || !pret2Id) {
            alert('Veuillez sélectionner deux prêts à comparer');
            return;
        }

        if (pret1Id === pret2Id) {
            alert('Veuillez sélectionner deux prêts différents');
            return;
        }

        try {
            // Récupérer les données des prêts sélectionnés
            const pret1Option = document.querySelector(`#select_pret_1 option[value="${pret1Id}"]`);
            const pret2Option = document.querySelector(`#select_pret_2 option[value="${pret2Id}"]`);

            loan1Data = JSON.parse(pret1Option.dataset.pret);
            loan2Data = JSON.parse(pret2Option.dataset.pret);

            // Simuler les calculs pour chaque prêt
            const simulation1 = await simulateLoan(loan1Data);
            const simulation2 = await simulateLoan(loan2Data);

            // Afficher les résultats
            displayLoanDetails(loan1Data, simulation1, 'loan1-details');
            displayLoanDetails(loan2Data, simulation2, 'loan2-details');
            displayComparisonTable(loan1Data, loan2Data, simulation1, simulation2);
            displayMetrics(simulation1, simulation2);
            displayRecommendation(loan1Data, loan2Data, simulation1, simulation2);

            document.getElementById('comparison-results').style.display = 'block';
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la comparaison: ' + error.message);
        }
    }

    async function compareManualLoans() {
        // Récupérer les valeurs des formulaires
        const pret1 = {
            nom: document.getElementById('pret1_nom').value,
            montant: parseFloat(document.getElementById('pret1_montant').value),
            taux: parseFloat(document.getElementById('pret1_taux').value),
            duree: parseInt(document.getElementById('pret1_duree').value)
        };

        const pret2 = {
            nom: document.getElementById('pret2_nom').value,
            montant: parseFloat(document.getElementById('pret2_montant').value),
            taux: parseFloat(document.getElementById('pret2_taux').value),
            duree: parseInt(document.getElementById('pret2_duree').value)
        };

        // Validation des données
        if (isNaN(pret1.montant) || isNaN(pret1.taux) || isNaN(pret1.duree) ||
            isNaN(pret2.montant) || isNaN(pret2.taux) || isNaN(pret2.duree)) {
            alert('Veuillez entrer des valeurs valides pour tous les champs');
            return;
        }

        try {
            // Simulation des prêts
            const simulation1 = await simulateLoan({
                montant_accorde: pret1.montant,
                taux_interet: pret1.taux,
                duree_mois: pret1.duree
            });

            const simulation2 = await simulateLoan({
                montant_accorde: pret2.montant,
                taux_interet: pret2.taux,
                duree_mois: pret2.duree
            });

            // Affichage des résultats
            displayLoanDetails(pret1, simulation1, 'loan1-details');
            displayLoanDetails(pret2, simulation2, 'loan2-details');
            displayComparisonTable(pret1, pret2, simulation1, simulation2);
            displayMetrics(simulation1, simulation2);
            displayRecommendation(pret1, pret2, simulation1, simulation2);

            document.getElementById('comparison-results').style.display = 'block';
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la comparaison: ' + error.message);
        }
    }

    async function simulateLoan(pret) {
        try {
            const response = await fetch(`${API_BASE}/remboursements/simuler`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    capital: parseFloat(pret.montant_accorde),
                    taux_annuel: parseFloat(pret.taux_interet),
                    duree_mois: parseInt(pret.duree_mois)
                })
            });

            const result = await response.json();
            if (result.success) {
                return result.data.simulation;
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Erreur simulation:', error);
            // Simulation basique en cas d'erreur
            const tauxMensuel = parseFloat(pret.taux_interet) / 100 / 12;
            const duree = parseInt(pret.duree_mois);
            const capital = parseFloat(pret.montant_accorde);

            const annuite = capital * (tauxMensuel * Math.pow(1 + tauxMensuel, duree)) / (Math.pow(1 + tauxMensuel, duree) - 1);
            const montantTotal = annuite * duree;
            const coutCredit = montantTotal - capital;

            return {
                capital: capital,
                taux_annuel: parseFloat(pret.taux_interet),
                duree_mois: duree,
                annuite: annuite,
                montant_total: montantTotal,
                cout_credit: coutCredit
            };
        }
    }

    function displayLoanDetails(pret, simulation, containerId) {
        const container = document.getElementById(containerId);
        container.innerHTML = `
            <div class="row">
                <div class="col-6">
                    <p><strong>Nom:</strong> ${pret.nom}</p>
                    <p><strong>Montant:</strong> ${formatCurrency(pret.montant || pret.montant_accorde)}</p>
                    <p><strong>Taux:</strong> ${formatPercentage(pret.taux || pret.taux_interet)}</p>
                </div>
                <div class="col-6">
                    <p><strong>Durée:</strong> ${pret.duree || pret.duree_mois} mois</p>
                    <p><strong>Annuité:</strong> ${formatCurrency(simulation.annuite)}</p>
                    <p><strong>Coût total:</strong> ${formatCurrency(simulation.cout_credit)}</p>
                </div>
            </div>
        `;
    }

    function displayComparisonTable(pret1, pret2, sim1, sim2) {
        const tbody = document.getElementById('comparison-table-body');
        tbody.innerHTML = '';

        const comparisons = [
            {
                critere: 'Montant Accordé',
                pret1: formatCurrency(pret1.montant || pret1.montant_accorde),
                pret2: formatCurrency(pret2.montant || pret2.montant_accorde),
                better: (pret1.montant || pret1.montant_accorde) > (pret2.montant || pret2.montant_accorde) ? 1 : 2
            },
            {
                critere: 'Taux d\'Intérêt',
                pret1: formatPercentage(pret1.taux || pret1.taux_interet),
                pret2: formatPercentage(pret2.taux || pret2.taux_interet),
                better: (pret1.taux || pret1.taux_interet) < (pret2.taux || pret2.taux_interet) ? 1 : 2
            },
            {
                critere: 'Durée',
                pret1: `${pret1.duree || pret1.duree_mois} mois`,
                pret2: `${pret2.duree || pret2.duree_mois} mois`,
                better: (pret1.duree || pret1.duree_mois) < (pret2.duree || pret2.duree_mois) ? 1 : 2
            },
            {
                critere: 'Annuité Mensuelle',
                pret1: formatCurrency(sim1.annuite),
                pret2: formatCurrency(sim2.annuite),
                better: sim1.annuite < sim2.annuite ? 1 : 2
            },
            {
                critere: 'Montant Total',
                pret1: formatCurrency(sim1.montant_total),
                pret2: formatCurrency(sim2.montant_total),
                better: sim1.montant_total < sim2.montant_total ? 1 : 2
            },
            {
                critere: 'Coût du Crédit',
                pret1: formatCurrency(sim1.cout_credit),
                pret2: formatCurrency(sim2.cout_credit),
                better: sim1.cout_credit < sim2.cout_credit ? 1 : 2
            }
        ];

        comparisons.forEach(comp => {
            const row = document.createElement('tr');
            const pret1Class = comp.better === 1 ? 'better-value' : comp.better === 2 ? 'worse-value' : '';
            const pret2Class = comp.better === 2 ? 'better-value' : comp.better === 1 ? 'worse-value' : '';

            row.innerHTML = `
                <td><strong>${comp.critere}</strong></td>
                <td class="${pret1Class}">${comp.pret1}</td>
                <td class="${pret2Class}">${comp.pret2}</td>
                <td>
                    ${comp.better === 1 ? '<i class="fas fa-arrow-left text-success"></i> Prêt 1' :
                comp.better === 2 ? 'Prêt 2 <i class="fas fa-arrow-right text-success"></i>' :
                    '<i class="fas fa-equals text-warning"></i> Égalité'}
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function displayMetrics(sim1, sim2) {
        const costDifference = Math.abs(sim1.cout_credit - sim2.cout_credit);
        const costPercentage = (costDifference / Math.min(sim1.cout_credit, sim2.cout_credit)) * 100;
        const durationDifference = Math.abs(sim1.duree_mois - sim2.duree_mois);
        const monthlyDifference = Math.abs(sim1.annuite - sim2.annuite);

        document.getElementById('metric-difference').textContent = `${costPercentage.toFixed(1)}%`;
        document.getElementById('metric-savings').textContent = formatCurrency(costDifference);
        document.getElementById('metric-duration').textContent = `${durationDifference} mois`;
        document.getElementById('metric-monthly').textContent = formatCurrency(monthlyDifference);
    }

    function displayRecommendation(pret1, pret2, sim1, sim2) {
        const container = document.getElementById('recommendation-text');
        let recommendation = '';

        if (sim1.cout_credit < sim2.cout_credit) {
            const savings = sim2.cout_credit - sim1.cout_credit;
            recommendation = `
                <p><strong>Recommandation: Prêt 1 (${pret1.nom})</strong></p>
                <p>Le premier prêt est plus avantageux avec des économies de ${formatCurrency(savings)} sur le coût total du crédit.</p>
                <ul>
                    <li>Coût total inférieur: ${formatCurrency(sim1.cout_credit)} vs ${formatCurrency(sim2.cout_credit)}</li>
                    <li>Annuité mensuelle: ${formatCurrency(sim1.annuite)}</li>
                    <li>Économies mensuelles: ${formatCurrency(sim2.annuite - sim1.annuite)}</li>
                </ul>
            `;
        } else if (sim2.cout_credit < sim1.cout_credit) {
            const savings = sim1.cout_credit - sim2.cout_credit;
            recommendation = `
                <p><strong>Recommandation: Prêt 2 (${pret2.nom})</strong></p>
                <p>Le deuxième prêt est plus avantageux avec des économies de ${formatCurrency(savings)} sur le coût total du crédit.</p>
                <ul>
                    <li>Coût total inférieur: ${formatCurrency(sim2.cout_credit)} vs ${formatCurrency(sim1.cout_credit)}</li>
                    <li>Annuité mensuelle: ${formatCurrency(sim2.annuite)}</li>
                    <li>Économies mensuelles: ${formatCurrency(sim1.annuite - sim2.annuite)}</li>
                </ul>
            `;
        } else {
            recommendation = `
                <p><strong>Recommandation: Équivalents</strong></p>
                <p>Les deux prêts ont un coût total similaire. Le choix peut se baser sur d'autres critères comme la durée ou l'annuité mensuelle.</p>
                <ul>
                    <li>Considérez la durée: ${pret1.duree || pret1.duree_mois} mois vs ${pret2.duree || pret2.duree_mois} mois</li>
                    <li>Considérez l'annuité mensuelle: ${formatCurrency(sim1.annuite)} vs ${formatCurrency(sim2.annuite)}</li>
                </ul>
            `;
        }

        container.innerHTML = recommendation;
    }

    function resetForm() {
        document.getElementById('form-comparaison').reset();
        document.getElementById('comparison-results').style.display = 'none';
    }

    function saveSimulation() {
        // Logique pour sauvegarder la simulation actuelle
        alert('Fonction de sauvegarde non implémentée');
    }

    async function loadSavedSimulations() {
        try {
            const listContainer = document.getElementById('saved-simulations-list');
            listContainer.innerHTML = '';
            fetch(API_BASE + 'simulations')
                .then(response => response.json())
                .then(data => {
                    data.forEach(simulation => {
                        const card = document.createElement('div');
                        card.className = 'card mb-3';
                        card.innerHTML = `
                        <div class="card-body">
                            <h5 class="card-title">${simulation.date_creation}</h5>
                            <p class="card-text">
                                Montant: ${formatCurrency(simulation.montant_total)}<br>
                                Cout de credit: ${formatCurrency(simulation.cout_credit)}<br>
                                Durée: ${simulation.duree} mois
                            </p>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary btn-sm me-2" onclick="viewSimulationDetails(${simulation.id})">
                                    <i class="fas fa-eye me-1"></i>Voir les Détails
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteSimulation(${simulation.id}, this)">
                                    <i class="fas fa-trash me-1"></i>Supprimer
                                </button>
                            </div>
                        </div>
                    `;
                        listContainer.appendChild(card);
                    });
                });

        } catch (error) {
            console.error('Erreur:', error);
            document.getElementById('saved-simulations-list').innerHTML = '<p class="text-danger">Erreur de chargement des simulations sauvegardées.</p>';
        }
    }

    function viewSimulationDetails(simulationId) {
        // Logique pour afficher les détails d'une simulation dans le modal
        const modal = new bootstrap.Modal(document.getElementById('simulationDetailsModal'));
        modal.show();

        // Charger les détails de la simulation via l'API
        fetch(`${API_BASE}simulations/${simulationId}/details`)
            .then(response => response.json())
            .then(result => {
                if (true) {
                    const simulation = result;

                    // Mettre à jour le contenu du modal avec les détails de la simulation
                    // document.getElementById('modal-summary-annuite').textContent = formatCurrency(simulation.annuite_mensuelle);
                    // document.getElementById('modal-summary-total').textContent = formatCurrency(simulation.montant_total);
                    // document.getElementById('modal-summary-cout').textContent = formatCurrency(simulation.cout_credit);
                    // document.getElementById('modal-summary-duree').textContent = simulation.duree;

                    // Remplir le tableau d'amortissement
                    const tbody = document.getElementById('modal-amortization-body');
                    tbody.innerHTML = '';

                    simulation.forEach(echeance => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td><strong>${echeance.numero_echeance}</strong></td>
                            <td>${formatCurrency(echeance.capital_restant_debut)}</td>
                            <td class="text-primary fw-bold">${formatCurrency(echeance.annuite)}</td>
                            <td class="text-warning">${formatCurrency(echeance.interet)}</td>
                            <td class="text-success">${formatCurrency(echeance.capital_rembourse)}</td>
                            <td>${formatCurrency(echeance.capital_restant_fin)}</td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    console.error('Erreur:', result.message);
                    alert('Erreur lors du chargement des détails de la simulation');
                }
            })
            .catch(error => {
                console.error('Erreur:', error.message);
            });
    }

    function deleteSimulation(simulationId, button) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette simulation ?')) {
            return;
        }

        fetch(`${API_BASE}remboursements/supprimer-simulation/${simulationId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Simulation supprimée avec succès');
                loadSavedSimulations();
            } else {
                throw new Error(result.message || 'Erreur de suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression de la simulation');
        });
    }

    async function saveCurrentSimulation() {
        if (!currentSimulationData) {
            alert('Aucune simulation à sauvegarder.');
            return;
        }

        // Demander un nom pour la simulation


        try {
            const response = await fetch(API_BASE + 'simulations', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    annuite_mensuelle: currentSimulationData.simulation.annuite,
                    montant_total: currentSimulationData.simulation.montant_total,
                    cout_credit: currentSimulationData.simulation.cout_credit,
                    duree: currentSimulationData.simulation.duree_mois
                })
            });
            console.log(JSON.stringify({
                annuite_mensuelle: currentSimulationData.simulation.annuite,
                montant_total: currentSimulationData.simulation.montant_total,
                cout_credit: currentSimulationData.simulation.cout_credit,
                duree: currentSimulationData.simulation.duree_mois
            }));
            const result = await response.json();

            if (true) {
                // Sauvegarder les détails de la simulation
                await saveSimulationDetails(result.id, currentSimulationData.tableau_amortissement);

                alert('Simulation sauvegardée avec succès !');

                // Activer l'onglet des simulations sauvegardées et charger la liste
                const savedTabEl = document.getElementById('saved-tab');
                const savedTab = new bootstrap.Tab(savedTabEl);
                savedTab.show();
                loadSavedSimulations();
            } else {
                throw new Error(result.message || 'Erreur lors de la sauvegarde');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la sauvegarde de la simulation: ' + error.message);
        }
    }

    async function saveSimulationDetails(simulationId, tableauAmortissement) {
        try {
            const response = await fetch(API_BASE + 'simulations/details', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    simulation_id: simulationId,
                    details: tableauAmortissement.map(echeance => ({
                        echeance: echeance.numero_echeance,
                        capital_restant_debut: echeance.capital_restant_debut,
                        annuite: echeance.annuite,
                        interet: echeance.interets,
                        capital_rembourse: echeance.capital_rembourse,
                        capital_restant_fin: echeance.capital_restant_fin
                    }))
                })
            });

            const result = await response.json();

            return true;
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la sauvegarde des détails: ' + error.message);
            return false;
        }
    }
</script>


</body>
</html>
