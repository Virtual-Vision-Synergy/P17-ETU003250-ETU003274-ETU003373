<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système Bancaire Étudiant - Approbation des Prêts</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1559526324-593bc073d938?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #0d6efd;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
        }

        .status-en_attente { background-color: #fd7e14; }
        .status-actif { background-color: #198754; }
        .status-rembourse { background-color: #0d6efd; }
        .status-refuse { background-color: #dc3545; }
        .status-defaut { background-color: #6c757d; }

        .pret-details {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Header avec navigation -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="../index.html">
                    <i class="fas fa-university me-2"></i>
                    <span class="fw-bold">Système Bancaire Étudiant</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="types-prets.php"><i class="fas fa-list me-1"></i> Types de Prêts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="etudiants.php"><i class="fas fa-user-graduate me-1"></i> Étudiants</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="etablissements.php"><i class="fas fa-building me-1"></i> Établissements</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="prets.php"><i class="fas fa-hand-holding-usd me-1"></i> Prêts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="approuver_prets.html"><i class="fas fa-check-circle me-1"></i> Approuver Prêts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="transactions.php"><i class="fas fa-exchange-alt me-1"></i> Transactions</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center py-4">
            <h1 class="display-4 fw-bold mb-3">Approbation des Prêts Étudiants</h1>
            <p class="lead mb-0">Approuvez ou refusez les demandes de prêts en attente</p>
        </div>
    </section>

    <!-- Toast de notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
        <div id="messageToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="messages">
                    Message de notification
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="py-5">
        <div class="container">
            <!-- Section sélection du prêt à approuver -->
            <section class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="h5 mb-0"><i class="fas fa-search me-2"></i> Sélectionner un prêt à approuver</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="select_pret" class="form-label">Prêt en attente <span class="text-danger">*</span></label>
                                <select class="form-select" id="select_pret" required onchange="afficherDetailsPret()">
                                    <option value="">Sélectionner un prêt</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button class="btn btn-primary" onclick="chargerPretsEnAttente()">
                                    <i class="fas fa-sync-alt me-2"></i> Actualiser la liste
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section détails du prêt et formulaire d'approbation -->
            <section class="mb-5 pret-details" id="section-details-pret">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h3 class="h5 mb-0"><i class="fas fa-check-circle me-2"></i> Approuver ou refuser le prêt</h3>
                    </div>
                    <div class="card-body">
                        <!-- Détails du prêt -->
                        <div class="mb-4">
                            <h4 class="h6 text-muted">Détails du prêt sélectionné</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: 200px;">ID du prêt</th>
                                            <td id="detail-id">-</td>
                                        </tr>
                                        <tr>
                                            <th>Étudiant</th>
                                            <td id="detail-etudiant">-</td>
                                        </tr>
                                        <tr>
                                            <th>Type de prêt</th>
                                            <td id="detail-type-pret">-</td>
                                        </tr>
                                        <tr>
                                            <th>Montant demandé</th>
                                            <td id="detail-montant-demande">-</td>
                                        </tr>
                                        <tr>
                                            <th>Taux d'intérêt</th>
                                            <td id="detail-taux">-</td>
                                        </tr>
                                        <tr>
                                            <th>Durée</th>
                                            <td id="detail-duree">-</td>
                                        </tr>
                                        <tr>
                                            <th>Date de demande</th>
                                            <td id="detail-date-demande">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Formulaire d'approbation -->
                        <form id="form-approbation" class="row g-3">
                            <h4 class="h6 text-muted">Informations d'approbation</h4>

                            <div class="col-md-6">
                                <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                <select class="form-select" id="statut" name="statut" required onchange="gererChangementStatut()">
                                    <option value="">Sélectionner un statut</option>
                                    <option value="actif">Approuvé (Actif)</option>
                                    <option value="refuse">Refusé</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="montant_accorde" class="form-label">Montant accordé (€) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="montant_accorde" name="montant_accorde" step="0.01" min="0" required>
                            </div>

                            <div class="col-md-6" id="date_debut_container">
                                <label for="date_debut" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut">
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <small><i class="fas fa-info-circle me-2"></i> Si vous refusez le prêt, le montant accordé sera automatiquement défini à 0 €.</small>
                                </div>
                            </div>

                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-success" onclick="approuverPret()">
                                    <i class="fas fa-check me-2"></i>Valider la décision
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Section liste des prêts récemment traités -->
            <section class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-history me-2 text-primary"></i> Prêts récemment traités</h2>
                    <button class="btn btn-primary" onclick="chargerPretsRecents()">
                        <i class="fas fa-sync-alt me-2"></i> Actualiser
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Étudiant</th>
                                <th>Type de prêt</th>
                                <th>Montant demandé</th>
                                <th>Montant accordé</th>
                                <th>Taux</th>
                                <th>Statut</th>
                                <th>Date d'approbation</th>
                                <th>Date de début</th>
                            </tr>
                        </thead>
                        <tbody id="liste-prets-recents">
                            <tr><td colspan="9" class="text-center">Chargement des données...</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Système Bancaire Étudiant</h5>
                    <p class="text-muted">Une solution complète de gestion des prêts et finances pour les étudiants, développée par l'Université ITU.</p>
                </div>

                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Liens rapides</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none"><i class="fas fa-question-circle me-2"></i>FAQ</a></li>
                        <li><a href="../API_DOCUMENTATION.md" class="text-light text-decoration-none"><i class="fas fa-book me-2"></i>Documentation API</a></li>
                        <li><a href="#" class="text-light text-decoration-none"><i class="fas fa-shield-alt me-2"></i>Politique de confidentialité</a></li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <h5 class="mb-3">Contact & Réseaux</h5>
                    <ul class="list-unstyled">
                        <li><a href="mailto:contact@banque-etudiant.edu" class="text-light text-decoration-none"><i class="fas fa-envelope me-2"></i>contact@banque-etudiant.edu</a></li>
                        <li><a href="tel:+2610000000" class="text-light text-decoration-none"><i class="fas fa-phone me-2"></i>+261 00 000 00</a></li>
                    </ul>
                    <div class="mt-3">
                        <a href="#" class="text-light me-3"><i class="fab fa-github fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>
            </div>

            <hr class="my-4 bg-light">

            <div class="text-center">
                <p class="mb-0">&copy; 2025 Système Bancaire Étudiant - Projet développé à l'Université ITU</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const API_BASE = '../ws/';
        let pretSelectionne = null;

        // Au chargement de la page
        window.onload = function() {
            chargerPretsEnAttente();
            chargerPretsRecents();
        };

        // Fonction pour charger les prêts en attente
        function chargerPretsEnAttente() {
            fetch(API_BASE + 'prets/in-process')
                .then(response => response.json())
                .then(data => {
                    const selectPret = document.getElementById('select_pret');
                    selectPret.innerHTML = '<option value="">Sélectionner un prêt</option>';

                    data.forEach(pret => {
                        const option = document.createElement('option');
                        option.value = pret.id;
                        option.textContent = `Prêt #${pret.id} - ${pret.etudiant_prenom} ${pret.etudiant_nom} - ${formatCurrency(pret.montant_demande)}`;
                        selectPret.appendChild(option);
                    });
                })
                .catch(error => showMessage('Erreur lors du chargement des prêts en attente', 'error'));
        }

        // Fonction pour afficher les détails d'un prêt sélectionné
        function afficherDetailsPret() {
            const pretId = document.getElementById('select_pret').value;

            if (!pretId) {
                document.getElementById('section-details-pret').style.display = 'none';
                pretSelectionne = null;
                return;
            }

            fetch(API_BASE + 'prets/' + pretId)
                .then(response => response.json())
                .then(pret => {
                    pretSelectionne = pret;

                    // Afficher les détails
                    document.getElementById('detail-id').textContent = pret.id;
                    document.getElementById('detail-etudiant').textContent = `${pret.etudiant_prenom} ${pret.etudiant_nom}`;
                    document.getElementById('detail-type-pret').textContent = pret.type_pret_nom;
                    document.getElementById('detail-montant-demande').textContent = formatCurrency(pret.montant_demande);
                    document.getElementById('detail-taux').textContent = `${pret.type_taux}%`;
                    document.getElementById('detail-duree').textContent = `${pret.duree_mois} mois`;
                    document.getElementById('detail-date-demande').textContent = formatDate(pret.date_demande);

                    // Par défaut, définir le montant accordé égal au montant demandé
                    document.getElementById('montant_accorde').value = pret.montant_demande;

                    // Définir la date de début par défaut à aujourd'hui
                    const today = new Date();
                    const formattedDate = today.toISOString().substring(0, 10);
                    document.getElementById('date_debut').value = formattedDate;

                    // Afficher la section
                    document.getElementById('section-details-pret').style.display = 'block';
                })
                .catch(error => showMessage('Erreur lors du chargement des détails du prêt', 'error'));
        }

        // Fonction pour gérer le changement de statut
        function gererChangementStatut() {
            const statut = document.getElementById('statut').value;
            const montantAccordeInput = document.getElementById('montant_accorde');
            const dateDebutContainer = document.getElementById('date_debut_container');

            if (statut === 'refuse') {
                montantAccordeInput.value = '0';
                montantAccordeInput.readOnly = true;
                dateDebutContainer.style.display = 'none';
            } else {
                montantAccordeInput.readOnly = false;
                if (pretSelectionne) {
                    montantAccordeInput.value = pretSelectionne.montant_demande;
                }
                dateDebutContainer.style.display = 'block';
            }
        }

        // Fonction pour approuver ou refuser un prêt
        function approuverPret() {
            if (!pretSelectionne) {
                showMessage('Aucun prêt sélectionné', 'error');
                return;
            }

            const statut = document.getElementById('statut').value;
            const montantAccorde = document.getElementById('montant_accorde').value;
            const dateDebut = document.getElementById('date_debut').value;

            if (!statut) {
                showMessage('Veuillez sélectionner un statut', 'error');
                return;
            }

            if (!montantAccorde) {
                showMessage('Veuillez entrer un montant accordé', 'error');
                return;
            }

            if (statut === 'actif' && !dateDebut) {
                showMessage('Veuillez sélectionner une date de début', 'error');
                return;
            }

            // Préparation des données pour l'API
            const data = {
                statut: statut,
                montant_accorde: parseFloat(montantAccorde)
            };

            if (statut === 'actif') {
                data.date_debut = dateDebut;
            }

            // Appel API pour approuver/refuser le prêt
            fetch(API_BASE + 'prets/' + pretSelectionne.id + '/approve', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Erreur lors de la mise à jour du statut du prêt');
                    });
                }
                return response.json();
            })
            .then(result => {
                const successMessage = statut === 'actif'
                    ? `Prêt #${pretSelectionne.id} approuvé avec succès !`
                    : `Prêt #${pretSelectionne.id} refusé avec succès !`;
                showMessage(successMessage, 'success');

                // Réinitialisation
                document.getElementById('select_pret').value = '';
                document.getElementById('section-details-pret').style.display = 'none';
                document.getElementById('statut').value = '';
                document.getElementById('montant_accorde').value = '';
                document.getElementById('date_debut').value = '';
                document.getElementById('montant_accorde').readOnly = false;
                document.getElementById('date_debut_container').style.display = 'block';
                pretSelectionne = null;

                // Actualiser les listes
                chargerPretsEnAttente();
                chargerPretsRecents();
            })
            .catch(error => showMessage(error.message || 'Erreur lors de l\'approbation du prêt', 'error'));
        }

        // Fonction pour charger les prêts récemment traités
        function chargerPretsRecents() {
            fetch(API_BASE + 'prets?statut=actif,refuse&limit=10')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('liste-prets-recents');
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="9" class="text-center">Aucun prêt récemment traité</td></tr>';
                        return;
                    }

                    data.forEach(pret => {
                        const row = tbody.insertRow();
                        const badgeClass = getStatutClass(pret.statut);

                        row.innerHTML = `
                            <td>${pret.id}</td>
                            <td>${pret.etudiant_prenom} ${pret.etudiant_nom}</td>
                            <td>${pret.type_pret_nom}</td>
                            <td>${formatCurrency(pret.montant_demande)}</td>
                            <td>${formatCurrency(pret.montant_accorde)}</td>
                            <td>${pret.type_taux}%</td>
                            <td><span class="badge ${badgeClass}">${pret.statut}</span></td>
                            <td>${formatDate(pret.date_approbation)}</td>
                            <td>${formatDate(pret.date_debut)}</td>
                        `;
                    });
                })
                .catch(error => showMessage('Erreur lors du chargement des prêts récents', 'error'));
        }

        // Fonctions utilitaires
        function showMessage(message, type = 'info') {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = message;

            // Définir la classe de la toast selon le type
            const toastEl = document.getElementById('messageToast');
            toastEl.className = 'toast align-items-center border-0';
            toastEl.classList.add(type === 'error' ? 'bg-danger' : 'bg-success', 'text-white');

            // Afficher la toast
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        }

        function formatDate(dateString) {
            return dateString ? new Date(dateString).toLocaleDateString('fr-FR') : 'Non défini';
        }

        function getStatutClass(statut) {
            const classes = {
                'en_attente': 'status-en_attente',
                'actif': 'status-actif',
                'rembourse': 'status-rembourse',
                'refuse': 'status-refuse',
                'defaut': 'status-defaut'
            };
            return classes[statut] || '';
        }
    </script>
</body>
</html>
