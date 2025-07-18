<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système Bancaire Étudiant - Établissements</title>

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

        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/etablissements.jpeg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
        }

        .form-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Header avec navigation -->
    <?php include('../includes/header.php') ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Gestion des Établissements Financiers</h1>
            <p class="lead">Administrez les établissements partenaires et gérez leurs fonds</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container py-5">
        <div id="messages" class="alert" role="alert" style="display: none;"></div>

        <div class="row g-4">
            <!-- Formulaire de création -->
            <div class="col-md-6">
                <div class="card form-section">
                    <div class="card-body">
                        <h2 class="card-title"><i class="fas fa-plus-circle text-primary me-2"></i>Créer un établissement</h2>
                        <form id="form-etablissement" class="mt-4">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <textarea class="form-control" id="adresse" name="adresse" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="fonds_disponibles" class="form-label">Fonds initiaux (€)</label>
                                <input type="number" class="form-control" id="fonds_disponibles" name="fonds_disponibles" step="0.01" min="0" value="0">
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary" onclick="resetFormEtablissement()">Annuler</button>
                                <button type="submit" class="btn btn-primary">Créer l'établissement</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Formulaire de dépôt -->
            <div class="col-md-6">
                <div class="card form-section">
                    <div class="card-body">
                        <h2 class="card-title"><i class="fas fa-coins text-primary me-2"></i>Ajouter des fonds</h2>
                        <form id="form-depot" class="mt-4">
                            <div class="mb-3">
                                <label for="depot_etablissement" class="form-label">Établissement</label>
                                <select class="form-select" id="depot_etablissement" name="depot_etablissement" required>
                                    <option value="">Choisir un établissement</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="depot_montant" class="form-label">Montant (€)</label>
                                <input type="number" class="form-control" id="depot_montant" name="depot_montant" step="0.01" min="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="depot_description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="depot_description" name="depot_description" placeholder="Dépôt de fonds">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Ajouter les fonds</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des établissements -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="card-title"><i class="fas fa-building text-primary me-2"></i>Liste des établissements</h2>
                    <button class="btn btn-outline-primary" onclick="loadEtablissements()">
                        <i class="fas fa-sync-alt me-1"></i> Actualiser
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Fonds disponibles (€)</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="liste-etablissements">
                            <tr><td colspan="6" class="text-center">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include('../includes/footer.php') ?>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="../assets/bootstrap.js"></script>

    <script>
        const API_BASE = '../ws/';

        function showMessage(message, type = 'info') {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.style.display = 'block';
            messagesDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
            messagesDiv.innerHTML = `${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;

            // Faire défiler jusqu'au message
            messagesDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Auto-masquer après 5 secondes
            setTimeout(() => {
                const alert = bootstrap.Alert.getOrCreateInstance(messagesDiv);
                alert.close();
            }, 5000);
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        }

        function loadEtablissements() {
            fetch(API_BASE + 'etablissements')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('liste-etablissements');

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Aucun établissement trouvé</td></tr>';
                        return;
                    }

                    tbody.innerHTML = '';
                    data.forEach(etablissement => {
                        const row = tbody.insertRow();
                        row.innerHTML = `
                            <td>${etablissement.id}</td>
                            <td>${etablissement.nom}</td>
                            <td>${formatCurrency(etablissement.fonds_disponibles)}</td>
                            <td>${etablissement.telephone || '-'}</td>
                            <td>${etablissement.email || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="ajouterFonds(${etablissement.id})">
                                    <i class="fas fa-coins me-1"></i> Ajouter Fonds
                                </button>
                            </td>
                        `;
                    });

                    // Mettre à jour la liste déroulante
                    loadEtablissementsSelect();
                })
                .catch(error => showMessage('Erreur lors du chargement des établissements', 'error'));
        }

        function loadEtablissementsSelect() {
            fetch(API_BASE + 'etablissements')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('depot_etablissement');
                    select.innerHTML = '<option value="">Choisir un établissement</option>';

                    data.forEach(etablissement => {
                        const option = document.createElement('option');
                        option.value = etablissement.id;
                        option.textContent = `${etablissement.nom} (${formatCurrency(etablissement.fonds_disponibles)})`;
                        select.appendChild(option);
                    });
                })
                .catch(error => showMessage('Erreur lors du chargement', 'error'));
        }

        document.getElementById('form-etablissement').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {
                nom: formData.get('nom'),
                adresse: formData.get('adresse'),
                telephone: formData.get('telephone'),
                email: formData.get('email'),
                fonds_disponibles: parseFloat(formData.get('fonds_disponibles'))
            };

            fetch(API_BASE + 'etablissements', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.error) {
                    showMessage(result.error, 'error');
                } else {
                    showMessage(`L'établissement "${data.nom}" a été créé avec succès !`);
                    this.reset();
                    loadEtablissements();
                }
            })
            .catch(error => showMessage('Erreur lors de la création', 'error'));
        });

        document.getElementById('form-depot').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const etablissementId = formData.get('depot_etablissement');
            const montant = parseFloat(formData.get('depot_montant'));
            const data = {
                montant: montant,
                description: formData.get('depot_description')
            };

            fetch(API_BASE + 'etablissements/' + etablissementId + '/depot', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.error) {
                    showMessage(result.error, 'error');
                } else {
                    showMessage(`Dépôt de ${formatCurrency(montant)} effectué avec succès !`);
                    this.reset();
                    loadEtablissements();
                }
            })
            .catch(error => showMessage('Erreur lors du dépôt', 'error'));
        });

        function resetFormEtablissement() {
            document.getElementById('form-etablissement').reset();
        }

        function ajouterFonds(id) {
            document.getElementById('depot_etablissement').value = id;
            // Faire défiler jusqu'au formulaire de dépôt
            document.getElementById('form-depot').scrollIntoView({ behavior: 'smooth' });
        }

        // Chargement initial
        document.addEventListener('DOMContentLoaded', function() {
            loadEtablissements();
        });
    </script>
</body>
</html>
