<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système Bancaire Étudiant - Gestion des Prêts</title>

    <!-- Bootstrap 5 CSS -->
    <link href="../assets/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="../assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/type-prets.jpeg');
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
    </style>
</head>
<body>
    <!-- Header avec navigation -->
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center py-4">
            <h1 class="display-4 fw-bold mb-3">Gestion des Prêts Étudiants</h1>
            <p class="lead mb-0">Suivez et administrez les demandes de prêts étudiants de manière efficace</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="py-5">
        <div class="container">
            <!-- Section formulaire d'ajout de prêt -->
            <section class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h3 class="h5 mb-0"><i class="fas fa-plus-circle me-2"></i> Nouveau prêt</h3>
                    </div>
                    <div class="card-body">
                        <form id="form-nouveau-pret" class="row g-3">
                            <div class="col-md-6">
                                <label for="etudiant_id" class="form-label">Étudiant <span class="text-danger">*</span></label>
                                <select class="form-select" id="etudiant_id" name="etudiant_id" required>
                                    <option value="">Sélectionner un étudiant</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="type_pret_id" class="form-label">Type de prêt <span class="text-danger">*</span></label>
                                <select class="form-select" id="type_pret_id" name="type_pret_id" required>
                                    <option value="">Sélectionner un type de prêt</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="etablissement_id" class="form-label">Établissement <span class="text-danger">*</span></label>
                                <select class="form-select" id="etablissement_id" name="etablissement_id" required>
                                    <option value="">Sélectionner un établissement</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="montant_demande" class="form-label">Montant demandé (€) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="montant_demande" name="montant_demande" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label for="delai" class="form-label">Délai (mois) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="delai" name="delai" min="1" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label for="duree_mois" class="form-label">Durée (mois) <span class="text-danger">*</span></label>
                                <select class="form-select" id="duree_mois" name="duree_mois" required>
                                    <option value="">Sélectionner une durée</option>
                                    <option value="12">12 mois (1 an)</option>
                                    <option value="24">24 mois (2 ans)</option>
                                    <option value="36">36 mois (3 ans)</option>
                                    <option value="48">48 mois (4 ans)</option>
                                    <option value="60">60 mois (5 ans)</option>
                                    <option value="72">72 mois (6 ans)</option>
                                    <option value="84">84 mois (7 ans)</option>
                                    <option value="96">96 mois (8 ans)</option>
                                    <option value="108">108 mois (9 ans)</option>
                                    <option value="120">120 mois (10 ans)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="assurance_pourcentage" class="form-label">Assurance (%) <span class="text-muted">(optionnel)</span></label>
                                <input type="number" class="form-control" id="assurance_pourcentage" name="assurance_pourcentage" step="0.01" min="0" max="100" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label for="but_pret" class="form-label">But du prêt</label>
                                <textarea class="form-control" id="but_pret" name="but_pret" rows="1" placeholder="Description du but du prêt"></textarea>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <small><i class="fas fa-info-circle me-2"></i> Le taux d'intérêt sera calculé en fonction du type de prêt et de la durée choisie. Le montant accordé sera déterminé après examen de la demande.</small>
                                </div>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-success" onclick="ajouterPret()">
                                    <i class="fas fa-check me-2"></i>Soumettre la demande
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Section liste des prêts -->
            <section class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-list-ul me-2 text-primary"></i> Liste des prêts</h2>
                    <button class="btn btn-primary" onclick="loadPrets()">
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
                                <th>Délai</th>
                                <th>Durée</th>
                                <th>Mensualité</th>
                                <th>Statut</th>
                                <th>Date demande</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="liste-prets">
                            <tr><td colspan="11" class="text-center">Chargement des données...</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="row">
                <!-- Section statistiques -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0"><i class="fas fa-chart-pie me-2"></i> Statistiques</h3>
                        </div>
                        <div class="card-body">
                            <div id="statistiques">
                                <div class="mb-3">
                                    <h4 class="h6 text-muted">Nombre total de prêts</h4>
                                    <p class="display-6 mb-0" id="stat-total">0</p>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <h4 class="h6 text-muted">En attente</h4>
                                        <p class="h4"><span class="badge status-en_attente" id="stat-attente">0</span></p>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <h4 class="h6 text-muted">Actifs</h4>
                                        <p class="h4"><span class="badge status-actif" id="stat-actifs">0</span></p>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="h6 text-muted">Remboursés</h4>
                                        <p class="h4"><span class="badge status-rembourse" id="stat-rembourses">0</span></p>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <h4 class="h6 text-muted">Montant total accordé</h4>
                                        <p class="h4 text-primary fw-bold" id="stat-montant-total">0 €</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section détails du prêt -->
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4" id="details-pret" style="display: none;">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0"><i class="fas fa-info-circle me-2"></i> Détails du prêt</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">ID du prêt</h4>
                                    <p class="mb-0" id="detail-id"></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Statut</h4>
                                    <p class="mb-0"><span id="detail-statut" class="fw-bold"></span></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Étudiant</h4>
                                    <p class="mb-0" id="detail-etudiant"></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Type de prêt</h4>
                                    <p class="mb-0" id="detail-type"></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Établissement</h4>
                                    <p class="mb-0" id="detail-etablissement"></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Montant demandé</h4>
                                    <p class="mb-0" id="detail-montant-demande"></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Montant accordé</h4>
                                    <p class="mb-0 fw-bold" id="detail-montant-accorde"></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Taux appliqué</h4>
                                    <p class="mb-0" id="detail-taux"></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Assurance</h4>
                                    <p class="mb-0" id="detail-assurance"></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Délai</h4>
                                    <p class="mb-0" id="detail-delai"></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Durée</h4>
                                    <p class="mb-0" id="detail-duree"></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Mensualité</h4>
                                    <p class="mb-0" id="detail-mensualite"></p>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <h4 class="h6 text-muted">Montant total</h4>
                                    <p class="mb-0 fw-bold text-primary" id="detail-montant-total"></p>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <h4 class="h5">Dates importantes</h4>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h4 class="h6 text-muted">Date de demande</h4>
                                    <p class="mb-0" id="detail-date-demande"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h4 class="h6 text-muted">Date d'approbation</h4>
                                    <p class="mb-0" id="detail-date-approbation"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h4 class="h6 text-muted">Date de début</h4>
                                    <p class="mb-0" id="detail-date-debut"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h4 class="h6 text-muted">Date de fin prévue</h4>
                                    <p class="mb-0" id="detail-date-fin"></p>
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <button class="btn btn-outline-secondary" onclick="document.getElementById('details-pret').style.display = 'none'">
                                    <i class="fas fa-times me-2"></i>Fermer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

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

    <!-- Bootstrap 5 JS Bundle with Popper -->
       <script src="../assets/bootstrap.js"></script>

    <script>
        const API_BASE = '../ws/';
        const toast = new bootstrap.Toast(document.getElementById('messageToast'));

        function showMessage(message, type = 'info') {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = `<div class="${type === 'error' ? 'text-danger' : 'text-success'}">${message}</div>`;

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

        function loadPrets() {
            fetch(API_BASE + 'prets')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('liste-prets');
                    tbody.innerHTML = '';

                    // Statistiques
                    let stats = {
                        total: data.length,
                        attente: 0,
                        actifs: 0,
                        rembourses: 0,
                        montantTotal: 0
                    };

                    data.forEach(pret => {
                        const row = tbody.insertRow();

                        // Calcul des statistiques
                        stats.montantTotal += parseFloat(pret.montant_accorde);
                        switch(pret.statut) {
                            case 'en_attente':
                                stats.attente++;
                                break;
                            case 'actif':
                                stats.actifs++;
                                break;
                            case 'rembourse':
                                stats.rembourses++;
                                break;
                        }

                        // Création du badge statut
                        const badgeClass = getStatutClass(pret.statut);

                        row.innerHTML = `
                            <td>${pret.id}</td>
                            <td>${pret.etudiant_prenom} ${pret.etudiant_nom}</td>
                            <td>${pret.type_pret_nom}</td>
                            <td>${formatCurrency(pret.montant_demande)}</td>
                            <td>${formatCurrency(pret.montant_accorde)}</td>
                            <td>${pret.type_taux}%</td>
                            <td>${pret.delai} mois</td>
                            <td>${pret.duree_mois} mois</td>
                            <td>${formatCurrency(pret.mensualite)}</td>
                            <td><span class="badge ${badgeClass}">${pret.statut}</span></td>
                            <td>${formatDate(pret.date_demande)}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button onclick="voirDetails(${pret.id})" class="btn btn-primary btn-sm" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="voirPdf(${pret.id})" class="btn btn-danger btn-sm" title="Télécharger le PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button onclick="voirPdf(${pret.id})" class="btn btn-info btn-sm" title="Afficher le PDF">
                                        <i class="fas fa-external-link-alt"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                    });

                    // Afficher les statistiques
                    document.getElementById('stat-total').textContent = stats.total;
                    document.getElementById('stat-attente').textContent = stats.attente;
                    document.getElementById('stat-actifs').textContent = stats.actifs;
                    document.getElementById('stat-rembourses').textContent = stats.rembourses;
                    document.getElementById('stat-montant-total').textContent = formatCurrency(stats.montantTotal);
                })
                .catch(error => showMessage('Erreur lors du chargement des prêts', 'error'));
        }

        function voirDetails(id) {
            fetch(API_BASE + 'prets/' + id)
                .then(response => response.json())
                .then(pret => {
                    if (pret.error) {
                        showMessage(pret.error, 'error');
                        return;
                    }

                    // Remplir les détails
                    document.getElementById('detail-id').textContent = pret.id;
                    document.getElementById('detail-etudiant').textContent = `${pret.etudiant_prenom} ${pret.etudiant_nom} (${pret.etudiant_email})`;
                    document.getElementById('detail-type').textContent = pret.type_pret_nom;
                    document.getElementById('detail-etablissement').textContent = pret.etablissement_nom;
                    document.getElementById('detail-montant-demande').textContent = formatCurrency(pret.montant_demande);
                    document.getElementById('detail-montant-accorde').textContent = formatCurrency(pret.montant_accorde);
                    document.getElementById('detail-taux').textContent = pret.type_taux + '%';
                    document.getElementById('detail-assurance').textContent = pret.assurance_pourcentage ? pret.assurance_pourcentage + '%' : 'Aucune';
                    document.getElementById('detail-delai').textContent = pret.delai + ' mois';
                    document.getElementById('detail-duree').textContent = pret.duree_mois + ' mois';
                    document.getElementById('detail-mensualite').textContent = formatCurrency(pret.mensualite);
                    document.getElementById('detail-montant-total').textContent = formatCurrency(pret.montant_total);

                    // Afficher le statut avec la bonne classe
                    const statusEl = document.getElementById('detail-statut');
                    statusEl.textContent = pret.statut;
                    statusEl.className = 'badge ' + getStatutClass(pret.statut);

                    document.getElementById('detail-date-demande').textContent = formatDate(pret.date_demande);
                    document.getElementById('detail-date-approbation').textContent = formatDate(pret.date_approbation);
                    document.getElementById('detail-date-debut').textContent = formatDate(pret.date_debut);
                    document.getElementById('detail-date-fin').textContent = formatDate(pret.date_fin_prevue);

                    // Afficher la section des détails
                    document.getElementById('details-pret').style.display = 'block';
                })
                .catch(error => showMessage('Erreur lors du chargement des détails', 'error'));
        }

        // Chargement initial
        document.addEventListener('DOMContentLoaded', function() {
            loadPrets();
            loadFormulaireDonnees();
        });

        // Charger les données pour les listes déroulantes du formulaire
        function loadFormulaireDonnees() {
            // Charger les étudiants
            fetch(API_BASE + 'etudiants')
                .then(response => response.json())
                .then(data => {
                    const selectEtudiant = document.getElementById('etudiant_id');
                    selectEtudiant.innerHTML = '<option value="">Sélectionner un étudiant</option>';

                    data.forEach(etudiant => {
                        const option = document.createElement('option');
                        option.value = etudiant.id;
                        option.textContent = `${etudiant.prenom} ${etudiant.nom} (${etudiant.email})`;
                        selectEtudiant.appendChild(option);
                    });
                })
                .catch(error => showMessage('Erreur lors du chargement des étudiants', 'error'));

            // Charger les types de prêts
            fetch(API_BASE + 'types-prets')
                .then(response => response.json())
                .then(data => {
                    const selectTypePret = document.getElementById('type_pret_id');
                    selectTypePret.innerHTML = '<option value="">Sélectionner un type de prêt</option>';

                    data.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.id;
                        option.textContent = `${type.nom} (taux: ${type.taux_interet}%)`;
                        selectTypePret.appendChild(option);
                    });
                })
                .catch(error => showMessage('Erreur lors du chargement des types de prêts', 'error'));

            // Charger les établissements
            fetch(API_BASE + 'etablissements')
                .then(response => response.json())
                .then(data => {
                    const selectEtablissement = document.getElementById('etablissement_id');
                    selectEtablissement.innerHTML = '<option value="">Sélectionner un établissement</option>';

                    data.forEach(etablissement => {
                        const option = document.createElement('option');
                        option.value = etablissement.id;
                        option.textContent = `${etablissement.nom} (${etablissement.adresse})`;
                        selectEtablissement.appendChild(option);
                    });
                })
                .catch(error => showMessage('Erreur lors du chargement des établissements', 'error'));
        }

        // Fonction pour ajouter un nouveau prêt
        function ajouterPret() {
            const form = document.getElementById('form-nouveau-pret');

            // Vérifier que le formulaire est valide
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Récupérer les données du formulaire
            const formData = {
                etudiant_id: parseInt(document.getElementById('etudiant_id').value),
                type_pret_id: parseInt(document.getElementById('type_pret_id').value),
                etablissement_id: parseInt(document.getElementById('etablissement_id').value),
                montant_demande: parseFloat(document.getElementById('montant_demande').value),
                delai: parseInt(document.getElementById('delai').value),
                duree_mois: parseInt(document.getElementById('duree_mois').value),
                assurance_pourcentage: parseFloat(document.getElementById('assurance_pourcentage').value) || null,
                but_pret: document.getElementById('but_pret').value || null,
                statut: 'en_attente' // Le statut par défaut est "en attente"
            };

            // Envoyer les données au serveur
            fetch(API_BASE + 'prets', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showMessage(data.error, 'error');
                } else {
                    // Réinitialiser le formulaire
                    form.reset();

                    // Actualiser la liste des prêts et afficher un message de succès
                    loadPrets();
                    showMessage('Demande de prêt ajoutée avec succès!', 'success');
                }
            })
            .catch(error => {
                showMessage('Erreur lors de l\'ajout du prêt', 'error');
                console.error(error);
            });
        }

        // Fonction pour générer et télécharger le PDF
        function genererPdf(id) {
            // Afficher un message de chargement
            showMessage('Génération du PDF en cours...', 'info');

            // Créer un lien temporaire pour télécharger le PDF
            const link = document.createElement('a');
            link.href = API_BASE + 'prets/' + id + '/pdf';
            link.download = 'contrat_pret_' + id + '.pdf';

            // Déclencher le téléchargement
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Afficher un message de succès après un court délai
            setTimeout(() => {
                showMessage('PDF généré et téléchargé avec succès!', 'success');
            }, 1000);
        }

        // Fonction pour afficher le PDF dans un nouvel onglet
        function voirPdf(id) {
            // Ouvrir le PDF dans un nouvel onglet
            const url = API_BASE + 'prets/' + id + '/pdf/view';
            window.open(url, '_blank');
        }
    </script>
</body>
</html>
