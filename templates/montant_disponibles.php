<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système Bancaire Étudiant - Montants Disponibles par Mois</title>

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
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/montant_disponibles.jpeg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 50px 0;
            margin-bottom: 30px;
        }

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
            <h1 class="display-4 fw-bold mb-3">Montants Disponibles par Mois</h1>
            <p class="lead">Suivez l'évolution des fonds disponibles pour chaque établissement</p>
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
                                <label for="filtre-annee" class="form-label">Année:</label>
                                <select id="filtre-annee" class="form-select">
                                    <option value="">Toutes les années</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="filtre-mois" class="form-label">Mois:</label>
                                <select id="filtre-mois" class="form-select">
                                    <option value="">Tous les mois</option>
                                    <option value="01">Janvier</option>
                                    <option value="02">Février</option>
                                    <option value="03">Mars</option>
                                    <option value="04">Avril</option>
                                    <option value="05">Mai</option>
                                    <option value="06">Juin</option>
                                    <option value="07">Juillet</option>
                                    <option value="08">Août</option>
                                    <option value="09">Septembre</option>
                                    <option value="10">Octobre</option>
                                    <option value="11">Novembre</option>
                                    <option value="12">Décembre</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="filtre-etablissement" class="form-label">Établissement:</label>
                                <select id="filtre-etablissement" class="form-select">
                                    <option value="">Tous les établissements</option>
                                </select>
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
                                <span><i class="fas fa-calendar-alt me-2"></i>Nombre de mois enregistrés:</span>
                                <span class="badge bg-primary rounded-pill" id="resume-total-mois">0</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-building me-2"></i>Nombre d'établissements:</span>
                                <span class="badge bg-info rounded-pill" id="resume-total-etablissements">0</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-wallet me-2"></i>Solde total actuel:</span>
                                <span class="badge bg-success rounded-pill" id="resume-solde-total">0 €</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-chart-line me-2"></i>Tendance sur 3 mois:</span>
                                <span class="badge bg-primary rounded-pill" id="resume-tendance">-</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tableau des montants disponibles -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0"><i class="fas fa-money-bill-wave me-2"></i>Montants disponibles par mois</h2>
                        <button class="btn btn-sm btn-light" onclick="loadMontantsDisponibles()">
                            <i class="fas fa-sync-alt me-1"></i>Actualiser
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mois</th>
                                        <th>Année</th>
                                        <th>Établissement</th>
                                        <th>Solde final (€)</th>
                                    </tr>
                                </thead>
                                <tbody id="liste-montants">
                                    <tr><td colspan="4" class="text-center">Chargement...</td></tr>
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
        let toutesDonnees = [];
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

        function getNomMois(moisNum) {
            const mois = [
                'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ];
            return mois[parseInt(moisNum) - 1];
        }

        function loadMontantsDisponibles() {
            fetch(API_BASE + 'transactions/last')
                .then(response => response.json())
                .then(data => {
                    toutesDonnees = data;
                    afficherMontantsDisponibles(data);
                    calculerResume(data);
                    chargerAnnees(data);
                    chargerEtablissements(data);
                })
                .catch(error => showMessage('Erreur lors du chargement des données', 'error'));
        }

        function chargerAnnees(data) {
            const annees = [...new Set(data.map(item => item.month.split('-')[0]))];
            const select = document.getElementById('filtre-annee');
            select.innerHTML = '<option value="">Toutes les années</option>';

            annees.sort((a, b) => b - a); // Tri décroissant

            annees.forEach(annee => {
                const option = document.createElement('option');
                option.value = annee;
                option.textContent = annee;
                select.appendChild(option);
            });
        }

        function chargerEtablissements(data) {
            const etablissements = [...new Set(data.map(item => item.etablissement_name))];
            const select = document.getElementById('filtre-etablissement');
            select.innerHTML = '<option value="">Tous les établissements</option>';

            etablissements.sort();

            etablissements.forEach(etab => {
                const option = document.createElement('option');
                option.value = etab;
                option.textContent = etab;
                select.appendChild(option);
            });
        }

        function afficherMontantsDisponibles(data) {
            const tbody = document.getElementById('liste-montants');

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">Aucune donnée trouvée</td></tr>';
                return;
            }

            tbody.innerHTML = '';

            data.forEach(item => {
                const [annee, mois] = item.month.split('-');
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td>${getNomMois(mois)}</td>
                    <td>${annee}</td>
                    <td>${item.etablissement_name}</td>
                    <td class="fw-bold">${formatCurrency(item.last_solde_apres)}</td>
                `;
            });
        }

        function calculerResume(data) {
            // Nombre de mois uniques
            const moisUniques = [...new Set(data.map(item => item.month))];
            const nombreMois = moisUniques.length;

            // Nombre d'établissements uniques
            const etablissementsUniques = [...new Set(data.map(item => item.etablissement_name))];
            const nombreEtablissements = etablissementsUniques.length;

            // Calculer le solde total actuel
            const soldeTotal = data.reduce((total, item) => total + parseFloat(item.last_solde_apres), 0);

            // Calculer la tendance sur les 3 derniers mois
            let tendance = "-";
            if (nombreMois >= 3) {
                // Trier les mois par ordre chronologique
                moisUniques.sort();
                // Prendre les 3 derniers mois
                const derniersMois = moisUniques.slice(-3);

                // Calculer le solde pour chaque mois
                const soldeParMois = derniersMois.map(mois => {
                    return data
                        .filter(item => item.month === mois)
                        .reduce((total, item) => total + parseFloat(item.last_solde_apres), 0);
                });

                // Calculer la différence entre le dernier et premier mois
                const difference = soldeParMois[2] - soldeParMois[0];
                const pourcentage = (difference / Math.abs(soldeParMois[0])) * 100;

                if (difference > 0) {
                    tendance = `<span class="variation-positive">+${pourcentage.toFixed(2)}%</span>`;
                } else if (difference < 0) {
                    tendance = `<span class="variation-negative">${pourcentage.toFixed(2)}%</span>`;
                } else {
                    tendance = "0% (stable)";
                }
            }

            // Afficher le résumé
            document.getElementById('resume-total-mois').textContent = nombreMois;
            document.getElementById('resume-total-etablissements').textContent = nombreEtablissements;
            document.getElementById('resume-solde-total').textContent = formatCurrency(soldeTotal);
            document.getElementById('resume-tendance').innerHTML = tendance;
        }

        function appliquerFiltres() {
            const annee = document.getElementById('filtre-annee').value;
            const mois = document.getElementById('filtre-mois').value;
            const etablissement = document.getElementById('filtre-etablissement').value;

            const donneesFiltrees = toutesDonnees.filter(item => {
                const [itemAnnee, itemMois] = item.month.split('-');

                return (
                    (annee === '' || itemAnnee === annee) &&
                    (mois === '' || itemMois === mois) &&
                    (etablissement === '' || item.etablissement_name === etablissement)
                );
            });

            afficherMontantsDisponibles(donneesFiltrees);
            calculerResume(donneesFiltrees);
            showMessage(`${donneesFiltrees.length} résultat(s) trouvé(s)`);
        }

        function reinitialiserFiltres() {
            document.getElementById('filtre-annee').value = '';
            document.getElementById('filtre-mois').value = '';
            document.getElementById('filtre-etablissement').value = '';

            afficherMontantsDisponibles(toutesDonnees);
            calculerResume(toutesDonnees);
            showMessage('Filtres réinitialisés');
        }

        // Chargement initial
        document.addEventListener('DOMContentLoaded', function() {
            loadMontantsDisponibles();
        });
    </script>
</body>
</html>
