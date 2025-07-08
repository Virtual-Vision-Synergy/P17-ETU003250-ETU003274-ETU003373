<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système Bancaire Étudiant - Gestion des Étudiants</title>

    <!-- Bootstrap 5 CSS -->
    <link href="../assets/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="../assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/etudiants.jpeg');
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
    <?php include('../includes/header.php') ?>
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
    <?php include('../includes/footer.php') ?>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="../assets/bootstrap.js"></script>

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
