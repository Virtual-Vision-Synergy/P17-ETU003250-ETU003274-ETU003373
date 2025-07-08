<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système Bancaire Étudiant - Types de Prêts</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1563013544-824ae1b704d3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
        }

        .form-section {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 1.5rem;
            color: #0d6efd;
            margin-right: 10px;
        }

        #messages {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1050;
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
                            <a class="nav-link active" href="types-prets.html"><i class="fas fa-list me-1"></i> Types de Prêts</a>
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
                            <a class="nav-link" href="transactions.php"><i class="fas fa-exchange-alt me-1"></i> Transactions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../index.html#api-section"><i class="fas fa-code me-1"></i> API REST</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Page Header -->
    <section class="page-header text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Gestion des Types de Prêts</h1>
            <p class="lead">Créez et gérez les différents types de prêts disponibles pour les étudiants</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="py-5">
        <div class="container">
            <div class="row">
                <!-- Formulaire de création -->
                <div class="col-lg-4 mb-4">
                    <div class="card form-section h-100">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0"><i class="fas fa-plus-circle me-2"></i>Nouveau Type de Prêt</h3>
                        </div>
                        <div class="card-body">
                            <form id="form-type-pret">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom du type de prêt</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="taux_interet" class="form-label">Taux d'intérêt (%)</label>
                                    <input type="number" class="form-control" id="taux_interet" name="taux_interet" step="0.01" min="0" max="20" required>
                                </div>

                                <div class="mb-3">
                                    <label for="duree_max_mois" class="form-label">Durée maximale (mois)</label>
                                    <input type="number" class="form-control" id="duree_max_mois" name="duree_max_mois" min="1" max="240" required>
                                </div>

                                <div class="mb-3">
                                    <label for="montant_min" class="form-label">Montant minimum (€)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        <input type="number" class="form-control" id="montant_min" name="montant_min" step="0.01" min="0" value="0">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="montant_max" class="form-label">Montant maximum (€)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        <input type="number" class="form-control" id="montant_max" name="montant_max" step="0.01" min="0">
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="actif" name="actif" checked>
                                    <label class="form-check-label" for="actif">Type de prêt actif</label>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                        <i class="fas fa-undo me-2"></i>Réinitialiser
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Liste des types de prêts -->
                <div class="col-lg-8">
                    <div class="card form-section">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="h5 mb-0"><i class="fas fa-list-ul me-2"></i>Liste des Types de Prêts</h3>
                            <button class="btn btn-sm btn-light" onclick="loadTypesPrets()">
                                <i class="fas fa-sync-alt me-1"></i>Actualiser
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Taux (%)</th>
                                            <th>Durée max</th>
                                            <th>Montant min</th>
                                            <th>Montant max</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="liste-types-prets">
                                        <tr><td colspan="9" class="text-center">Chargement...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Zone de notification des messages -->
    <div id="messages"></div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Système Bancaire Étudiant</h5>
                    <p>Une solution complète pour la gestion des prêts étudiants et des services bancaires associés.</p>
                    <p class="mb-0">Version 1.0</p>
                </div>

                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Liens utiles</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none"><i class="fas fa-file-alt me-2"></i>Conditions Générales d'Utilisation</a></li>
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

        function showMessage(message, type = 'info') {
            const messagesDiv = document.getElementById('messages');
            const alertClass = type === 'error' ? 'danger' : 'success';

            messagesDiv.innerHTML = `
                <div class="alert alert-${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                }
            }, 5000);
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        }

        function loadTypesPrets() {
            const tableBody = document.getElementById('liste-types-prets');
            tableBody.innerHTML = '<tr><td colspan="9" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></td></tr>';

            fetch(API_BASE + 'types-prets')
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = '';

                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="9" class="text-center">Aucun type de prêt trouvé</td></tr>';
                        return;
                    }

                    data.forEach(type => {
                        const row = tableBody.insertRow();
                        row.innerHTML = `
                            <td>${type.id}</td>
                            <td>${type.nom}</td>
                            <td>${type.description || '<em class="text-muted">Non spécifié</em>'}</td>
                            <td><span class="badge bg-info">${type.taux_interet}%</span></td>
                            <td>${type.duree_max_mois} mois</td>
                            <td>${formatCurrency(type.montant_min)}</td>
                            <td>${type.montant_max ? formatCurrency(type.montant_max) : '<em>Illimité</em>'}</td>
                            <td>${type.actif ? '<span class="badge bg-success">Actif</span>' : '<span class="badge bg-secondary">Inactif</span>'}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editTypePret(${type.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteTypePret(${type.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                    });
                })
                .catch(error => showMessage('Erreur lors du chargement des types de prêts', 'error'));
        }

        document.getElementById('form-type-pret').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {
                nom: formData.get('nom'),
                description: formData.get('description'),
                taux_interet: parseFloat(formData.get('taux_interet')),
                duree_max_mois: parseInt(formData.get('duree_max_mois')),
                montant_min: parseFloat(formData.get('montant_min')),
                montant_max: formData.get('montant_max') ? parseFloat(formData.get('montant_max')) : null,
                actif: formData.has('actif')
            };

            fetch(API_BASE + 'types-prets', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.error) {
                    showMessage(result.error, 'error');
                } else {
                    showMessage('Le type de prêt a été créé avec succès', 'success');
                    this.reset();
                    loadTypesPrets();
                }
            })
            .catch(error => showMessage('Erreur lors de la création', 'error'));
        });

        function resetForm() {
            document.getElementById('form-type-pret').reset();
        }

        function editTypePret(id) {
            // Fonctionnalité d'édition - peut être implémentée ultérieurement
            showMessage("La fonctionnalité d'édition sera disponible prochainement", 'info');
        }

        function deleteTypePret(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce type de prêt ?')) {
                fetch(API_BASE + 'types-prets/' + id, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(result => {
                    showMessage('Le type de prêt a été supprimé avec succès', 'success');
                    loadTypesPrets();
                })
                .catch(error => showMessage('Erreur lors de la suppression', 'error'));
            }
        }

        // Chargement initial
        document.addEventListener('DOMContentLoaded', function() {
            loadTypesPrets();
        });
    </script>
</body>
</html>
