<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système Bancaire Étudiant - Gestion des Remboursements</title>

    <!-- Bootstrap 5 CSS -->
    <link href="../assets/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="../assets/fontawesome-free-6.7.2-web/css/all.min.css">

    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/remboursements.jpeg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
        }


        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
        }

        .status-en_attente {
            background-color: #fd7e14;
        }

        .status-paye {
            background-color: #198754;
        }

        .status-retard {
            background-color: #dc3545;
        }

        .summary-item {
            background: white;
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
        }

        .automatic-payment-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border: 1px solid #90caf9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .pagination-controls {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
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
            <i class="fas fa-calendar-check me-3"></i>
            Gestion des Remboursements
        </h1>
        <p class="lead">
            Gérez les remboursements des prêts étudiants avec paiements automatiques
        </p>
    </div>
</section>

<!-- Contenu Principal -->
<div class="container mt-5">
    <!-- Onglets -->
    <ul class="nav nav-tabs" id="remboursementTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="liste-tab" data-bs-toggle="tab" data-bs-target="#liste" type="button"
                    role="tab">
                <i class="fas fa-list me-2"></i>Liste des Remboursements
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="paiement-tab" data-bs-toggle="tab" data-bs-target="#paiement" type="button"
                    role="tab">
                <i class="fas fa-credit-card me-2"></i>Effectuer un Paiement
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="retards-tab" data-bs-toggle="tab" data-bs-target="#retards" type="button"
                    role="tab">
                <i class="fas fa-exclamation-triangle me-2"></i>Remboursements en Retard
            </button>
        </li>
    </ul>

    <!-- Contenu des Onglets -->
    <div class="tab-content" id="remboursementTabContent">
        <!-- Onglet Liste des Remboursements -->
        <div class="tab-pane fade show active" id="liste" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-list me-2"></i>Liste des Remboursements</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Étudiant</th>
                                <th>N° Échéance</th>
                                <th>Montant Prévu</th>
                                <th>Date Échéance</th>
                                <th>Montant Payé</th>
                                <th>Date Paiement</th>
                                <th>Statut</th>
                            </tr>
                            </thead>
                            <tbody id="remboursements-list">
                            <!-- Chargé par JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Effectuer un Paiement - VERSION AUTOMATIQUE -->
        <div class="tab-pane fade" id="paiement" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Effectuer un Paiement Automatique</h4>
                </div>
                <div class="card-body">
                    <!-- Information sur le paiement automatique -->
                    <div class="automatic-payment-info">
                        <div class="row align-items-center">
                            <div class="col-md-1 text-center">
                                <i class="fas fa-robot fa-2x text-primary"></i>
                            </div>
                            <div class="col-md-11">
                                <h5 class="mb-2 text-primary"><i class="fas fa-info-circle me-2"></i>Paiement
                                    Automatique</h5>
                                <p class="mb-0 text-muted">
                                    Le montant payé correspond automatiquement à l'annuité mensuelle calculée.
                                    Sélectionnez simplement l'échéance à payer - le système s'occupe du reste !
                                </p>
                            </div>
                        </div>
                    </div>

                    <form id="form-paiement">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="select_remboursement" class="form-label">
                                        <i class="fas fa-calendar-check me-2"></i>Remboursement à payer
                                    </label>
                                    <select class="form-select" id="select_remboursement" required>
                                        <option value="">Chargement...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-calculator me-2"></i>Montant automatique (€)
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light text-center fw-bold"
                                               id="montant_affiche" readonly
                                               placeholder="Sélectionnez un remboursement">
                                        <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-euro-sign"></i>
                                            </span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-cog me-1"></i>Calculé automatiquement selon l'annuité mensuelle
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0" role="alert">
                            <div class="row align-items-center">
                                <div class="col-md-1 text-center">
                                    <i class="fas fa-lightbulb fa-lg text-info"></i>
                                </div>
                                <div class="col-md-11">
                                    <strong>Comment ça marche :</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Le montant est calculé automatiquement selon l'annuité mensuelle</li>
                                        <li>Les pénalités de retard sont ajoutées automatiquement si applicable</li>
                                        <li>Aucune saisie manuelle de montant n'est requise</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" disabled id="btn-paiement">
                                <i class="fas fa-check-circle me-2"></i>Effectuer le Paiement Automatique
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Onglet Remboursements en Retard -->
        <div class="tab-pane fade" id="retards" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Remboursements en Retard</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>N° Échéance</th>
                                <th>Montant Dû</th>
                                <th>Date Échéance</th>
                                <th>Jours de Retard</th>
                                <th>Statut</th>
                            </tr>
                            </thead>
                            <tbody id="retards-list">
                            <!-- Chargé par JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
    document.addEventListener('DOMContentLoaded', function () {
        loadRemboursementsList();
        loadRemboursementsNonPayes();
        loadRemboursementsEnRetard();
        loadExistingLoans();

        // Gestionnaire d'événements pour le formulaire de paiement
        document.getElementById('form-paiement').addEventListener('submit', handlePaiement);

        // Gestionnaire pour la sélection d'un remboursement
        const selectRemboursement = document.getElementById('select_remboursement');
        const montantAffiche = document.getElementById('montant_affiche');
        const btnPaiement = document.getElementById('btn-paiement');

        selectRemboursement.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];

            if (this.value && selectedOption.dataset.montant) {
                const montant = parseFloat(selectedOption.dataset.montant);
                montantAffiche.value = formatCurrency(montant);
                btnPaiement.disabled = false;
            } else {
                montantAffiche.value = '';
                btnPaiement.disabled = true;
            }
        });

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

    function getStatusBadge(status) {
        const badges = {
            'en_attente': '<span class="status-badge status-en_attente">En Attente</span>',
            'paye': '<span class="status-badge status-paye">Payé</span>',
            'retard': '<span class="status-badge status-retard">En Retard</span>'
        };
        return badges[status] || status;
    }

    async function loadRemboursementsNonPayes() {
        try {
            const response = await fetch(API_BASE + 'remboursements/non-payes');
            const result = await response.json();

            const select = document.getElementById('select_remboursement');
            select.innerHTML = '<option value="">Sélectionner un remboursement...</option>';

            if (result.success && result.data.length > 0) {
                result.data.forEach(remb => {
                    const option = document.createElement('option');
                    option.value = remb.id;
                    option.innerHTML = `${remb.etudiant_nom} - Échéance #${remb.numero_echeance} - ${formatCurrency(remb.montant_prevu)} - ${new Date(remb.date_echeance).toLocaleDateString('fr-FR')}`;
                    option.dataset.montant = remb.montant_prevu;
                    select.appendChild(option);
                });
            } else {
                select.innerHTML = '<option value="">Aucun remboursement en attente</option>';
            }
        } catch (error) {
            console.error('Erreur:', error);
            document.getElementById('select_remboursement').innerHTML = '<option value="">Erreur de chargement</option>';
        }
    }

    async function handlePaiement(e) {
        e.preventDefault();

        const remboursementId = document.getElementById('select_remboursement').value;

        if (!remboursementId) {
            alert('Veuillez sélectionner un remboursement');
            return;
        }

        try {
            const response = await fetch(API_BASE + 'remboursements/paiement', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    remboursement_id: remboursementId
                })
            });

            const result = await response.json();

            if (result.success) {
                const data = result.data;
                let message = `✅ Paiement automatique effectué avec succès!\n\n`;
                message += `💰 Montant payé: ${formatCurrency(data.montant_paye)}\n`;
                if (data.penalite > 0) {
                    message += `⚠️ Pénalité de retard: ${formatCurrency(data.penalite)}\n`;
                }
                message += `📊 Montant total: ${formatCurrency(data.montant_total)}`;

                alert(message);
                document.getElementById('form-paiement').reset();
                document.getElementById('montant_affiche').value = '';
                document.getElementById('btn-paiement').disabled = true;
                loadRemboursementsNonPayes();
                loadRemboursementsList();
            } else {
                throw new Error(result.message || 'Erreur lors du paiement');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('❌ Erreur lors du paiement: ' + error.message);
        }
    }

    async function loadRemboursementsList() {
        try {
            const response = await fetch(API_BASE + 'remboursements');
            const result = await response.json();

            const tbody = document.getElementById('remboursements-list');
            tbody.innerHTML = '';

            if (result.success && result.data.length > 0) {
                result.data.forEach(remb => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${remb.id}</td>
                        <td>${remb.etudiant_nom} ${remb.etudiant_prenom}</td>
                        <td>${remb.numero_echeance}</td>
                        <td>${formatCurrency(remb.montant_prevu)}</td>
                        <td>${new Date(remb.date_echeance).toLocaleDateString('fr-FR')}</td>
                        <td>${remb.montant_paye ? formatCurrency(remb.montant_paye) : '-'}</td>
                        <td>${remb.date_paiement ? new Date(remb.date_paiement).toLocaleDateString('fr-FR') : '-'}</td>
                        <td>${getStatusBadge(remb.statut)}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">Aucun remboursement trouvé</td></tr>';
            }
        } catch (error) {
            console.error('Erreur:', error);
            document.getElementById('remboursements-list').innerHTML = '<tr><td colspan="8" class="text-center text-danger">Erreur de chargement</td></tr>';
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

    async function loadRemboursementsEnRetard() {
        try {
            const response = await fetch(API_BASE + 'remboursements/retard');
            const result = await response.json();

            const tbody = document.getElementById('retards-list');
            tbody.innerHTML = '';

            if (result.success && result.data.length > 0) {
                result.data.forEach(remb => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${remb.etudiant_nom} ${remb.etudiant_prenom}</td>
                        <td>${remb.numero_echeance}</td>
                        <td>${formatCurrency(remb.montant_prevu)}</td>
                        <td>${new Date(remb.date_echeance).toLocaleDateString('fr-FR')}</td>
                        <td><span class="badge bg-danger">${remb.jours_retard} jours</span></td>
                        <td>${getStatusBadge(remb.statut)}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-success">Aucun remboursement en retard</td></tr>';
            }
        } catch (error) {
            console.error('Erreur:', error);
            document.getElementById('retards-list').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erreur de chargement</td></tr>';
        }
    }

</script>
</body>
</html>
