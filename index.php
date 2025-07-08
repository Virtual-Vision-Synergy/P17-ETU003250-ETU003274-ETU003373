<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système Bancaire Étudiant - Accueil</title>

    <!-- Bootstrap 5 CSS -->
    <link href="assets/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/index.jpeg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
        }

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

        .trust-icon {
            font-size: 2rem;
            color: #0d6efd;
        }

        .teaser-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            transform: rotate(10deg);
        }
    </style>
</head>
<body>
<!-- Header avec navigation -->
<?php include 'includes/header_index.php'; ?>
    <!-- Hero Section -->
    <section class="hero-section position-relative">
        <div class="container text-center py-5">
            <h1 class="display-4 fw-bold mb-3">Simplifiez la gestion des prêts étudiants</h1>
            <p class="lead mb-4">Une plateforme complète pour la gestion des services bancaires dédiés aux étudiants</p>
            <div class="position-absolute top-0 end-0 m-4">
                <span class="badge bg-danger p-3 teaser-badge">
                    <i class="fas fa-bolt me-1"></i> Nouveau : prêts mobilité à taux réduit !
                </span>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main>
        <!-- Fonctionnalités principales -->
        <section class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5">Nos fonctionnalités principales</h2>
                <div class="row g-4">
                    <!-- Création de types de prêts -->
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-box bg-white p-4">
                            <div class="text-center">
                                <i class="fas fa-list-ul feature-icon"></i>
                                <h3 class="h4">Création de types de prêts</h3>
                            </div>
                            <p>Créez et gérez différents types de prêts avec des taux personnalisés, des montants adaptés et des durées flexibles.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Prêts standard</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Équipement informatique</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Logement étudiant</li>
                            </ul>
                            <a href="templates/types-prets.php" class="btn btn-outline-primary mt-2">En savoir plus</a>
                        </div>
                    </div>

                    <!-- Gestion des étudiants -->
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-box bg-white p-4">
                            <div class="text-center">
                                <i class="fas fa-user-graduate feature-icon"></i>
                                <h3 class="h4">Gestion des étudiants</h3>
                            </div>
                            <p>Enregistrez et administrez les profils des étudiants clients de votre établissement bancaire en toute simplicité.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Profils détaillés</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Historique de crédit</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Suivi personnalisé</li>
                            </ul>
                            <a href="templates/etudiants.php" class="btn btn-outline-primary mt-2">En savoir plus</a>
                        </div>
                    </div>

                    <!-- Suivi des établissements financiers -->
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-box bg-white p-4">
                            <div class="text-center">
                                <i class="fas fa-building feature-icon"></i>
                                <h3 class="h4">Établissements financiers</h3>
                            </div>
                            <p>Gérez les fonds disponibles et les dépôts des établissements financiers partenaires avec une traçabilité complète.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Suivi des fonds</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Partenariats optimisés</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Rapports détaillés</li>
                            </ul>
                            <a href="templates/etablissements.php" class="btn btn-outline-primary mt-2">En savoir plus</a>
                        </div>
                    </div>

                    <!-- Demandes et statuts des prêts -->
                    <div class="col-md-6 col-lg-6">
                        <div class="feature-box bg-white p-4">
                            <div class="text-center">
                                <i class="fas fa-hand-holding-usd feature-icon"></i>
                                <h3 class="h4">Demandes et statuts des prêts</h3>
                            </div>
                            <p>Suivez les demandes de prêts, leur approbation et gérez leur cycle de vie complet du dépôt jusqu'au remboursement.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Workflow d'approbation</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Notifications automatisées</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Suivi des remboursements</li>
                            </ul>
                            <a href="templates/prets.php" class="btn btn-outline-primary mt-2">En savoir plus</a>
                        </div>
                    </div>

                    <!-- Historique des transactions -->
                    <div class="col-md-6 col-lg-6">
                        <div class="feature-box bg-white p-4">
                            <div class="text-center">
                                <i class="fas fa-exchange-alt feature-icon"></i>
                                <h3 class="h4">Historique des transactions</h3>
                            </div>
                            <p>Consultez et analysez l'historique complet des transactions financières pour une transparence totale et une traçabilité assurée.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Filtrage avancé</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Exportation des données</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Reporting financier</li>
                            </ul>
                            <a href="templates/transactions.php" class="btn btn-outline-primary mt-2">En savoir plus</a>
                        </div>
                    </div>

                    <!-- Approbation des prêts -->
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-box bg-white p-4">
                            <div class="text-center">
                                <i class="fas fa-check-double feature-icon"></i>
                                <h3 class="h4">Approbation des prêts</h3>
                            </div>
                            <p>Gérez efficacement le processus d'approbation des demandes de prêts avec un workflow personnalisable et des critères d'évaluation.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Workflow d'approbation</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Critères personnalisés</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Notifications en temps réel</li>
                            </ul>
                            <a href="templates/approuver_prets.php" class="btn btn-outline-primary mt-2">En savoir plus</a>
                        </div>
                    </div>

                    <!-- Gestion des intérêts -->
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-box bg-white p-4">
                            <div class="text-center">
                                <i class="fas fa-percentage feature-icon"></i>
                                <h3 class="h4">Gestion des intérêts</h3>
                            </div>
                            <p>Configurez et suivez les taux d'intérêt appliqués aux différents types de prêts avec des calculs automatisés précis.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Taux personnalisables</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Calculs automatisés</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Historique des taux</li>
                            </ul>
                            <a href="templates/interets.php" class="btn btn-outline-primary mt-2">En savoir plus</a>
                        </div>
                    </div>

                    <!-- Montants disponibles -->
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-box bg-white p-4">
                            <div class="text-center">
                                <i class="fas fa-coins feature-icon"></i>
                                <h3 class="h4">Montants disponibles</h3>
                            </div>
                            <p>Surveillez et gérez les montants disponibles pour les prêts en temps réel avec des alertes de seuil personnalisables.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Suivi en temps réel</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Alertes de seuil</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Répartition par établissement</li>
                            </ul>
                            <a href="templates/montant_disponibles.php" class="btn btn-outline-primary mt-2">En savoir plus</a>
                        </div>
                    </div>

                    <!-- Gestion des remboursements -->
                    <div class="col-md-6 col-lg-6">
                        <div class="feature-box bg-white p-4">
                            <div class="text-center">
                                <i class="fas fa-calendar-check feature-icon"></i>
                                <h3 class="h4">Gestion des remboursements</h3>
                            </div>
                            <p>Planifiez et suivez les échéances de remboursement avec des rappels automatiques et une gestion flexible des retards.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Calendrier des échéances</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Rappels automatiques</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Gestion des retards</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Historique des paiements</li>
                            </ul>
                            <a href="templates/remboursements.php" class="btn btn-outline-primary mt-2">En savoir plus</a>
                        </div>
                    </div>

                    <!-- Simulations de prêts -->
                    <div class="col-md-6 col-lg-6">
                        <div class="feature-box bg-white p-4">
                            <div class="text-center">
                                <i class="fas fa-calculator feature-icon"></i>
                                <h3 class="h4">Simulations de prêts</h3>
                            </div>
                            <p>Proposez des simulations de prêts interactives permettant aux étudiants d'estimer leurs mensualités et coûts totaux.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Calculs interactifs</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Comparaison de scénarios</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Graphiques visuels</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Export des résultats</li>
                            </ul>
                            <a href="templates/simulations.php" class="btn btn-outline-primary mt-2">En savoir plus</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Informations de confiance et engagement -->
        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Des services bancaires auxquels vous pouvez faire confiance</h2>

                <!-- Statistiques -->
                <div class="row mb-5 text-center">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="border rounded p-4">
                            <h3 class="display-4 fw-bold text-primary">250+</h3>
                            <p class="lead">Prêts actifs</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="border rounded p-4">
                            <h3 class="display-4 fw-bold text-primary">3.2M€</h3>
                            <p class="lead">Fonds disponibles</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-4">
                            <h3 class="display-4 fw-bold text-primary">98%</h3>
                            <p class="lead">Taux de satisfaction</p>
                        </div>
                    </div>
                </div>

                <!-- Mentions et badges de confiance -->
                <div class="row g-4 text-center">
                    <div class="col-md-3">
                        <i class="fas fa-shield-alt trust-icon mb-3"></i>
                        <h4>Sécurité</h4>
                        <p class="text-muted">Données cryptées et protocoles sécurisés</p>
                    </div>
                    <div class="col-md-3">
                        <i class="fas fa-chart-line trust-icon mb-3"></i>
                        <h4>Transparence</h4>
                        <p class="text-muted">Conditions claires et sans frais cach��s</p>
                    </div>
                    <div class="col-md-3">
                        <i class="fas fa-code-branch trust-icon mb-3"></i>
                        <h4>Projet open-source</h4>
                        <p class="text-muted">Code accessible et communauté active</p>
                    </div>
                    <div class="col-md-3">
                        <i class="fas fa-graduation-cap trust-icon mb-3"></i>
                        <h4>Développé à l'ITU</h4>
                        <p class="text-muted">Par des étudiants pour des étudiants</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section storytelling / À propos -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <img src="https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Équipe de développement" class="img-fluid rounded shadow">
                    </div>
                    <div class="col-lg-6">
                        <h2>À propos du projet</h2>
                        <p class="lead">Notre mission : simplifier l'accès aux prêts étudiants</p>
                        <p>Le Système Bancaire Étudiant est né de la volonté de créer une plateforme accessible et transparente pour la gestion des prêts étudiants. Développé dans le cadre d'un projet de groupe au semestre 4 en Informatique et Design à l'Université ITU, ce système offre une solution complète pour les établissements financiers souhaitant proposer des services adaptés aux besoins des étudiants.</p>

                        <h5 class="mt-4">Technologies utilisées</h5>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-secondary">PHP</span>
                            <span class="badge bg-secondary">Flight Framework</span>
                            <span class="badge bg-secondary">MySQL</span>
                            <span class="badge bg-secondary">Bootstrap 5</span>
                            <span class="badge bg-secondary">JavaScript</span>
                            <span class="badge bg-secondary">AJAX</span>
                            <span class="badge bg-secondary">REST API</span>
                        </div>

                        <a href="https://github.com/Virtual-Vision-Synergy/P17-ETU003250-ETU003274-ETU003373" class="btn btn-outline-dark mt-2">
                            <i class="fab fa-github me-2"></i>Voir sur GitHub
                        </a>
                        <a href="https://github.com/orgs/Virtual-Vision-Synergy/projects/8" class="btn btn-outline-dark mt-2">
                            <i class="fab fa-github me-2"></i>Notre todo list
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- API REST Section -->
        <section id="api-section" class="py-5">
            <div class="container">
                <div class="text-center mb-4">
                    <h2>API REST</h2>
                    <p class="lead">Le système dispose d'une API REST complète pour une intégration facile</p>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Endpoint</th>
                                <th>Description</th>
                                <th>Méthode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>/ws/types-prets</code></td>
                                <td>Liste des types de prêts</td>
                                <td><span class="badge bg-success">GET</span></td>
                            </tr>
                            <tr>
                                <td><code>/ws/etudiants</code></td>
                                <td>Liste des étudiants</td>
                                <td><span class="badge bg-success">GET</span></td>
                            </tr>
                            <tr>
                                <td><code>/ws/etablissements</code></td>
                                <td>Liste des établissements</td>
                                <td><span class="badge bg-success">GET</span></td>
                            </tr>
                            <tr>
                                <td><code>/ws/prets</code></td>
                                <td>Liste des prêts</td>
                                <td><span class="badge bg-success">GET</span></td>
                            </tr>
                            <tr>
                                <td><code>/ws/transactions</code></td>
                                <td>Historique des transactions</td>
                                <td><span class="badge bg-success">GET</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-4">
                    <a href="API_DOCUMENTATION.md" class="btn btn-primary">
                        <i class="fas fa-book me-2"></i>Documentation complète de l'API
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
  <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap 5 JS Bundle with Popper -->
       <script src="assets/bootstrap.js"></script>
</body>
</html>

