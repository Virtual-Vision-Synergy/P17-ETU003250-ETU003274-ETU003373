<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système Bancaire Étudiant - Historique des Transactions</title>

    <!-- Bootstrap 5 CSS -->
    <link href="../assets/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="../assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .feature-box {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            height: 100%;
            transition: transform 0.3s;
        }

        .feature-box:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #0d6efd;
        }

        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/transactions.jpeg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 50px 0;
            margin-bottom: 30px;
        }

        .transaction-type-depot { color: #198754; } /* green */
        .transaction-type-pret { color: #dc3545; } /* red */
        .transaction-type-remboursement { color: #0d6efd; } /* blue */
        .transaction-type-penalite { color: #fd7e14; } /* orange */

        .variation-positive { color: #198754; }
        .variation-negative { color: #dc3545; }
    </style>
</head>
<body>
    <!-- Header avec navigation -->
    <?php include '../includes/header.php'; ?>
    <!-- En-tête de page -->
    <div class="page-header text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Historique des Transactions</h1>
            <p class="lead">Suivez, analysez et filtrez toutes les transactions financières</p>
        </div>
    </div>

    <main class="container py-5">
        <div class="row">
            <!-- Filtres -->
            <div class="col-lg-4 mb-5">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0"><i class="fas fa-filter me-2"></i>Filtres</h2>
                    </div>
                    <div class="card-body">
                        <form id="form-filtres">
                            <div class="mb-3">
                                <label for="filtre-etablissement" class="form-label">Établissement:</label>
                                <select id="filtre-etablissement" class="form-select">
                                    <option value="">Tous les établissements</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="filtre-type" class="form-label">Type de transaction:</label>
                                <select id="filtre-type" class="form-select">
                                    <option value="">Tous les types</option>
                                    <option value="depot">Dépôt</option>
                                    <option value="pret_accorde">Prêt accordé</option>
                                    <option value="remboursement_recu">Remboursement reçu</option>
                                    <option value="penalite">Pénalité</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="filtre-montant-min" class="form-label">Montant minimum (€):</label>
                                <input type="number" id="filtre-montant-min" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="mb-3">
                                <label for="filtre-montant-max" class="form-label">Montant maximum (€):</label>
                                <input type="number" id="filtre-montant-max" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" onclick="appliquerFiltres()">
                                    <i class="fas fa-search me-2"></i>Appliquer les filtres
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="reinitialiserFiltres()">
                                    <i class="fas fa-undo me-2"></i>Réinitialiser
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Résumé financier -->
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0"><i class="fas fa-chart-pie me-2"></i>Résumé financier</h2>
                    </div>
                    <div class="card-body" id="resume-financier">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-list me-2"></i>Nombre total de transactions:</span>
                                <span class="badge bg-primary rounded-pill" id="resume-total">0</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-arrow-circle-up me-2 text-success"></i>Total des dépôts:</span>
                                <span class="badge bg-success rounded-pill" id="resume-depots">0 €</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-arrow-circle-down me-2 text-danger"></i>Total des prêts accordés:</span>
                                <span class="badge bg-danger rounded-pill" id="resume-prets">0 €</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-reply me-2 text-primary"></i>Total des remboursements:</span>
                                <span class="badge bg-primary rounded-pill" id="resume-remboursements">0 €</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-exclamation-circle me-2 text-warning"></i>Total des pénalités:</span>
                                <span class="badge bg-warning text-dark rounded-pill" id="resume-penalites">0 €</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-wallet me-2"></i>Solde actuel total:</span>
                                <span class="badge bg-info rounded-pill" id="resume-solde-total">0 €</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Historique des transactions -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0"><i class="fas fa-history me-2"></i>Historique des transactions</h2>
                        <button class="btn btn-sm btn-light" onclick="loadTransactions()">
                            <i class="fas fa-sync-alt me-1"></i>Actualiser
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Date/Heure</th>
                                        <th>Établissement</th>
                                        <th>Type</th>
                                        <th>Montant (€)</th>
                                        <th>Solde avant</th>
                                        <th>Solde après</th>
                                        <th>Variation</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody id="liste-transactions">
                                    <tr><td colspan="9" class="text-center">Chargement...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages système -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="messages" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong class="me-auto">Notification</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- Bootstrap 5 JS Bundle with Popper -->
       <script src="../assets/bootstrap.js"></script>

    <script>
        const API_BASE = '../ws/';
        let toutesTransactions = [];
        const toast = new bootstrap.Toast(document.getElementById('messages'));

        function showMessage(message, type = 'info') {
            const messagesDiv = document.getElementById('messages');
            const toastBody = messagesDiv.querySelector('.toast-body');
            const icon = messagesDiv.querySelector('.toast-header i');

            toastBody.textContent = message;

            // Réinitialiser les classes
            icon.className = 'fas me-2';

            if (type === 'error') {
                messagesDiv.classList.add('bg-danger', 'text-white');
                icon.classList.add('fa-exclamation-circle');
            } else {
                messagesDiv.classList.add('bg-success', 'text-white');
                icon.classList.add('fa-check-circle');
            }

            toast.show();
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        }

        function formatDateTime(dateString) {
            return new Date(dateString).toLocaleString('fr-FR');
        }

        function getTypeColor(type) {
            const colors = {
                'depot': 'transaction-type-depot',
                'pret_accorde': 'transaction-type-pret',
                'remboursement_recu': 'transaction-type-remboursement',
                'penalite': 'transaction-type-penalite'
            };
            return colors[type] || '';
        }

        function getTypeLabel(type) {
            const labels = {
                'depot': 'Dépôt',
                'pret_accorde': 'Prêt accordé',
                'remboursement_recu': 'Remboursement',
                'penalite': 'Pénalité'
            };
            return labels[type] || type;
        }

        function getTypeIcon(type) {
            const icons = {
                'depot': '<i class="fas fa-arrow-circle-up me-1"></i>',
                'pret_accorde': '<i class="fas fa-arrow-circle-down me-1"></i>',
                'remboursement_recu': '<i class="fas fa-reply me-1"></i>',
                'penalite': '<i class="fas fa-exclamation-circle me-1"></i>'
            };
            return icons[type] || '';
        }

        function calculerVariation(avant, apres) {
            const variation = apres - avant;
            const classeVariation = variation >= 0 ? 'variation-positive' : 'variation-negative';
            const symbole = variation >= 0 ? '+' : '';
            return `<span class="${classeVariation}">${symbole}${formatCurrency(variation)}</span>`;
        }

        function loadTransactions() {
            fetch(API_BASE + 'transactions')
                .then(response => response.json())
                .then(data => {
                    toutesTransactions = data;
                    afficherTransactions(data);
                    calculerResume(data);
                    chargerEtablissementsFiltres();
                })
                .catch(error => showMessage('Erreur lors du chargement des transactions', 'error'));
        }

        function afficherTransactions(transactions) {
            const tbody = document.getElementById('liste-transactions');

            if (transactions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">Aucune transaction trouvée</td></tr>';
                return;
            }

            tbody.innerHTML = '';

            transactions.forEach(transaction => {
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td>${transaction.id}</td>
                    <td>${formatDateTime(transaction.date_transaction)}</td>
                    <td>${transaction.etablissement_nom}</td>
                    <td class="${getTypeColor(transaction.type_transaction)}">${getTypeIcon(transaction.type_transaction)}${getTypeLabel(transaction.type_transaction)}</td>
                    <td class="fw-bold">${formatCurrency(transaction.montant)}</td>
                    <td>${formatCurrency(transaction.solde_avant)}</td>
                    <td>${formatCurrency(transaction.solde_apres)}</td>
                    <td>${calculerVariation(transaction.solde_avant, transaction.solde_apres)}</td>
                    <td>${transaction.description || '-'}</td>
                `;
            });
        }

        function calculerResume(transactions) {
            let resume = {
                total: transactions.length,
                depots: 0,
                prets: 0,
                remboursements: 0,
                penalites: 0,
                soldeTotal: 0
            };

            transactions.forEach(transaction => {
                const montant = parseFloat(transaction.montant);

                switch(transaction.type_transaction) {
                    case 'depot':
                        resume.depots += montant;
                        break;
                    case 'pret_accorde':
                        resume.prets += montant;
                        break;
                    case 'remboursement_recu':
                        resume.remboursements += montant;
                        break;
                    case 'penalite':
                        resume.penalites += montant;
                        break;
                }
            });

            // Calculer le solde total actuel
            fetch(API_BASE + 'etablissements')
                .then(response => response.json())
                .then(etablissements => {
                    resume.soldeTotal = etablissements.reduce((total, etab) => total + parseFloat(etab.fonds_disponibles), 0);

                    // Afficher le résumé
                    document.getElementById('resume-total').textContent = resume.total;
                    document.getElementById('resume-depots').textContent = formatCurrency(resume.depots);
                    document.getElementById('resume-prets').textContent = formatCurrency(resume.prets);
                    document.getElementById('resume-remboursements').textContent = formatCurrency(resume.remboursements);
                    document.getElementById('resume-penalites').textContent = formatCurrency(resume.penalites);
                    document.getElementById('resume-solde-total').textContent = formatCurrency(resume.soldeTotal);
                });
        }

        function chargerEtablissementsFiltres() {
            fetch(API_BASE + 'etablissements')
                .then(response => response.json())
                .then(etablissements => {
                    const select = document.getElementById('filtre-etablissement');
                    // Garder l'option "Tous"
                    select.innerHTML = '<option value="">Tous les établissements</option>';

                    etablissements.forEach(etablissement => {
                        const option = document.createElement('option');
                        option.value = etablissement.nom;
                        option.textContent = etablissement.nom;
                        select.appendChild(option);
                    });
                })
                .catch(error => showMessage('Erreur lors du chargement des établissements', 'error'));
        }

        function appliquerFiltres() {
            const etablissement = document.getElementById('filtre-etablissement').value;
            const type = document.getElementById('filtre-type').value;
            const montantMin = parseFloat(document.getElementById('filtre-montant-min').value) || 0;
            const montantMax = parseFloat(document.getElementById('filtre-montant-max').value) || Infinity;

            const transactionsFiltrees = toutesTransactions.filter(transaction => {
                const montant = parseFloat(transaction.montant);

                return (
                    (etablissement === '' || transaction.etablissement_nom === etablissement) &&
                    (type === '' || transaction.type_transaction === type) &&
                    (montant >= montantMin && montant <= montantMax)
                );
            });

            afficherTransactions(transactionsFiltrees);
            calculerResume(transactionsFiltrees);
            showMessage(`${transactionsFiltrees.length} transaction(s) trouvée(s)`);
        }

        function reinitialiserFiltres() {
            document.getElementById('filtre-etablissement').value = '';
            document.getElementById('filtre-type').value = '';
            document.getElementById('filtre-montant-min').value = '';
            document.getElementById('filtre-montant-max').value = '';

            afficherTransactions(toutesTransactions);
            calculerResume(toutesTransactions);
            showMessage('Filtres réinitialisés');
        }

        // Chargement initial
        document.addEventListener('DOMContentLoaded', function() {
            loadTransactions();
        });
    </script>
</body>
</html>
