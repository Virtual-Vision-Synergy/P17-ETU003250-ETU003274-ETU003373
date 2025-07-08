<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système Bancaire Étudiant - Gestion des Étudiants</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
        }
        .table-actions {
            white-space: nowrap;
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
                            <a class="nav-link active" href="etudiants.html"><i class="fas fa-user-graduate me-1"></i> Étudiants</a>
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
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- En-tête de page -->
    <section class="page-header">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Gestion des Étudiants</h1>
            <p class="lead">Gérez les profils des étudiants bénéficiaires des prêts</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="py-5">
        <div class="container">
            <div class="row">
                <!-- Formulaire d'ajout d'étudiant -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0"><i class="fas fa-user-plus me-2"></i>Ajouter un étudiant</h5>
                        </div>
                        <div class="card-body">
                            <form id="form-etudiant">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required>
                                </div>
                                <div class="mb-3">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="age" class="form-label">Âge</label>
                                    <input type="number" class="form-control" id="age" name="age" min="16" max="100" required>
                                </div>
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone">
                                </div>
                                <div class="mb-3">
                                    <label for="adresse" class="form-label">Adresse</label>
                                    <textarea class="form-control" id="adresse" name="adresse" rows="3"></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer
                                    </button>
                                    <button type="button" onclick="resetForm()" class="btn btn-secondary">
                                        <i class="fas fa-undo me-2"></i>Réinitialiser
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Liste des étudiants -->
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>Liste des étudiants</h5>
                            <button onclick="loadEtudiants()" class="btn btn-sm btn-light">
                                <i class="fas fa-sync-alt me-1"></i>Actualiser
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Email</th>
                                            <th>Âge</th>
                                            <th>Téléphone</th>
                                            <th>Date création</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="liste-etudiants">
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Chargement...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast pour les notifications -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
            <div id="toast-message" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header" id="toast-header">
                    <strong class="me-auto" id="toast-title">Notification</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body" id="toast-body"></div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5">
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
        const toastElement = new bootstrap.Toast(document.getElementById('toast-message'), {
            delay: 5000
        });

        function showMessage(message, type = 'info') {
            const toast = document.getElementById('toast-message');
            const toastHeader = document.getElementById('toast-header');
            const toastBody = document.getElementById('toast-body');
            const toastTitle = document.getElementById('toast-title');

            toastBody.textContent = message;

            // Réinitialiser les classes
            toastHeader.className = 'toast-header';

            // Appliquer les styles en fonction du type
            if (type === 'error') {
                toastHeader.classList.add('bg-danger', 'text-white');
                toastTitle.textContent = 'Erreur';
            } else {
                toastHeader.classList.add('bg-success', 'text-white');
                toastTitle.textContent = 'Succès';
            }

            bootstrap.Toast.getOrCreateInstance(toast).show();
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('fr-FR');
        }

        function loadEtudiants() {
            fetch(API_BASE + 'etudiants')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('liste-etudiants');
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Aucun étudiant trouvé</td></tr>';
                        return;
                    }

                    data.forEach(etudiant => {
                        const row = tbody.insertRow();
                        row.innerHTML = `
                            <td>${etudiant.id}</td>
                            <td>${etudiant.nom}</td>
                            <td>${etudiant.prenom}</td>
                            <td>${etudiant.email}</td>
                            <td>${etudiant.age}</td>
                            <td>${etudiant.telephone || '<span class="text-muted">Non renseigné</span>'}</td>
                            <td>${formatDate(etudiant.date_creation)}</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-outline-info" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteEtudiant(${etudiant.id})" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                    });
                })
                .catch(error => showMessage('Erreur lors du chargement des étudiants', 'error'));
        }

        document.getElementById('form-etudiant').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {
                nom: formData.get('nom'),
                prenom: formData.get('prenom'),
                email: formData.get('email'),
                age: parseInt(formData.get('age')),
                telephone: formData.get('telephone'),
                adresse: formData.get('adresse')
            };

            fetch(API_BASE + 'etudiants', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.error) {
                    showMessage(result.error, 'error');
                } else {
                    showMessage(result.message || 'Étudiant ajouté avec succès!', 'success');
                    this.reset();
                    loadEtudiants();
                }
            })
            .catch(error => showMessage('Erreur lors de l\'ajout', 'error'));
        });

        function resetForm() {
            document.getElementById('form-etudiant').reset();
        }

        function deleteEtudiant(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?')) {
                fetch(API_BASE + 'etudiants/' + id, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(result => {
                    showMessage(result.message || 'Étudiant supprimé avec succès', 'success');
                    loadEtudiants();
                })
                .catch(error => showMessage('Erreur lors de la suppression', 'error'));
            }
        }

        // Chargement initial
        document.addEventListener('DOMContentLoaded', function() {
            loadEtudiants();
        });
    </script>
</body>
</html>
