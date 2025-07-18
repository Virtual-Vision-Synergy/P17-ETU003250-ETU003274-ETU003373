<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Intérêts - Système Bancaire Étudiant</title>

  <!-- Bootstrap 5 CSS -->
    <link href="../assets/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="../assets/fontawesome-free-6.7.2-web/css/all.min.css">
  <!-- Chart.js -->
  <script src="../assets/charts.js"></script>

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
      transform: translateY(-5px);
    }

    .stats-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 10px;
      padding: 1.5rem;
      margin-bottom: 1rem;
    }

    .chart-container {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    .filter-section {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      border-radius: 10px;
      padding: 2rem;
      margin-bottom: 2rem;
    }
  </style>
</head>
<body>
<!-- Header avec navigation -->
<?php include '../includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-primary text-white py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1 class="display-5 fw-bold mb-3">
          <i class="fas fa-chart-line me-3"></i>
          Gestion des Intérêts Mensuels
        </h1>
        <p class="lead">Analysez et calculez les intérêts générés par les prêts étudiants avec des outils de visualisation avancés</p>
      </div>
      <div class="col-lg-4 text-center">
        <i class="fas fa-calculator fa-5x opacity-75"></i>
      </div>
    </div>
  </div>
</section>

<main class="py-5">
  <div class="container">
    <!-- Filtres de recherche -->
    <section class="mb-5">
      <div class="filter-section">
        <h2 class="mb-4">
          <i class="fas fa-filter me-2"></i>
          Filtres de recherche
        </h2>
        <form id="filterForm">
          <div class="row g-3">
            <div class="col-md-6 col-lg-4">
              <label for="etablissement_id" class="form-label">Établissement</label>
              <select id="etablissement_id" name="etablissement_id" class="form-select">
                <option value="">Tous les établissements</option>
              </select>
            </div>
            <div class="col-md-6 col-lg-2">
              <label for="annee_debut" class="form-label">Année début</label>
              <select id="annee_debut" name="annee_debut" class="form-select">
                <option value="">Année début</option>
              </select>
            </div>
            <div class="col-md-6 col-lg-2">
              <label for="mois_debut" class="form-label">Mois début</label>
              <select id="mois_debut" name="mois_debut" class="form-select">
                <option value="">Mois début</option>
                <option value="1">Janvier</option>
                <option value="2">Février</option>
                <option value="3">Mars</option>
                <option value="4">Avril</option>
                <option value="5">Mai</option>
                <option value="6">Juin</option>
                <option value="7">Juillet</option>
                <option value="8">Août</option>
                <option value="9">Septembre</option>
                <option value="10">Octobre</option>
                <option value="11">Novembre</option>
                <option value="12">Décembre</option>
              </select>
            </div>
            <div class="col-md-6 col-lg-2">
              <label for="annee_fin" class="form-label">Année fin</label>
              <select id="annee_fin" name="annee_fin" class="form-select">
                <option value="">Année fin</option>
              </select>
            </div>
            <div class="col-md-6 col-lg-2">
              <label for="mois_fin" class="form-label">Mois fin</label>
              <select id="mois_fin" name="mois_fin" class="form-select">
                <option value="">Mois fin</option>
                <option value="1">Janvier</option>
                <option value="2">Février</option>
                <option value="3">Mars</option>
                <option value="4">Avril</option>
                <option value="5">Mai</option>
                <option value="6">Juin</option>
                <option value="7">Juillet</option>
                <option value="8">Août</option>
                <option value="9">Septembre</option>
                <option value="10">Octobre</option>
                <option value="11">Novembre</option>
                <option value="12">Décembre</option>
              </select>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-12">
              <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-search me-1"></i>
                Filtrer
              </button>
              <button type="button" id="calculerInterets" class="btn btn-success">
                <i class="fas fa-calculator me-1"></i>
                Calculer Intérêts
              </button>
            </div>
          </div>
        </form>
      </div>
    </section>

    <!-- Statistiques des intérêts -->
    <section class="mb-5">
      <h2 class="mb-4">
        <i class="fas fa-chart-bar me-2"></i>
        Statistiques des intérêts
      </h2>
      <div class="row g-3">
        <div class="col-md-6 col-lg-4">
          <div class="feature-box bg-white border">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-euro-sign fa-2x text-success"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="mb-1">Total des intérêts</h6>
                <h4 class="text-success mb-0" id="totalInterets">0 €</h4>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-box bg-white border">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-chart-line fa-2x text-info"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="mb-1">Moyenne mensuelle</h6>
                <h4 class="text-info mb-0" id="moyenneInterets">0 €</h4>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-box bg-white border">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-arrow-up fa-2x text-warning"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="mb-1">Maximum mensuel</h6>
                <h4 class="text-warning mb-0" id="maxInterets">0 €</h4>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-box bg-white border">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-calendar fa-2x text-primary"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="mb-1">Nombre de périodes</h6>
                <h4 class="text-primary mb-0" id="nombrePeriodes">0</h4>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-box bg-white border">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-piggy-bank fa-2x text-secondary"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="mb-1">Capital total</h6>
                <h4 class="text-secondary mb-0" id="totalCapital">0 €</h4>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-box bg-white border">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-handshake fa-2x text-danger"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="mb-1">Prêts actifs total</h6>
                <h4 class="text-danger mb-0" id="totalPretsActifs">0</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Graphique d'évolution des intérêts -->
    <section class="mb-5">
      <div class="chart-container">
        <h2 class="mb-4">
          <i class="fas fa-chart-area me-2"></i>
          Graphique d'évolution des intérêts
        </h2>
        <div style="height: 400px;">
          <canvas id="interetsChart" style="width: 100%; height: 100%;"></canvas>
        </div>
      </div>
    </section>

    <!-- Liste des intérêts mensuels -->
    <section>
      <div class="feature-box bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="mb-0">
            <i class="fas fa-table me-2"></i>
            Liste des intérêts mensuels
          </h2>
          <button onclick="loadInteretsData()" class="btn btn-outline-primary">
            <i class="fas fa-sync-alt me-1"></i>
            Actualiser
          </button>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
            <tr>
              <th>Établissement</th>
              <th>Période</th>
              <th>Année</th>
              <th>Mois</th>
              <th>Montant Intérêts (€)</th>
              <th>Prêts Actifs</th>
              <th>Capital Total (€)</th>
              <th>Date Calcul</th>
            </tr>
            </thead>
            <tbody id="interetsTableBody">
            <tr><td colspan="8" class="text-center">Chargement...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- Message d'alerte -->
    <div id="message" class="alert alert-dismissible fade show d-none" role="alert">
      <span id="messageText"></span>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  </div>
</main>

<!-- Footer -->
<?php include '../includes/footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="../assets/bootstrap.js"></script>

<script>
  const API_BASE = '../ws/';
  let interetsChart = null;

  function showMessage(message, type = 'info') {
    const messageDiv = document.getElementById('message');
    const messageText = document.getElementById('messageText');
    messageText.textContent = message;

    // Supprimer les anciennes classes
    messageDiv.classList.remove('alert-success', 'alert-danger', 'alert-info', 'd-none');

    // Ajouter la nouvelle classe selon le type
    if (type === 'error') {
      messageDiv.classList.add('alert-danger');
    } else if (type === 'success') {
      messageDiv.classList.add('alert-success');
    } else {
      messageDiv.classList.add('alert-info');
    }

    setTimeout(() => {
      messageDiv.classList.add('d-none');
    }, 5000);
  }

  document.addEventListener('DOMContentLoaded', function() {
    // Initialisation
    loadEtablissements();
    loadYears();
    loadInteretsData();

    // Gestionnaire de formulaire
    document.getElementById('filterForm').addEventListener('submit', function(e) {
      e.preventDefault();
      loadInteretsData();
    });

    // Gestionnaire pour calculer les intérêts
    document.getElementById('calculerInterets').addEventListener('click', function() {
      calculerInterets();
    });
  });

  // Charger les établissements
  function loadEtablissements() {
    fetch(API_BASE + 'etablissements')
            .then(response => response.json())
            .then(data => {
              const select = document.getElementById('etablissement_id');
              data.forEach(function(etablissement) {
                const option = document.createElement('option');
                option.value = etablissement.id;
                option.textContent = etablissement.nom;
                select.appendChild(option);
              });
            })
            .catch(error => {
              console.error('Erreur lors du chargement des établissements:', error);
              showMessage('Erreur lors du chargement des établissements', 'error');
            });
  }

  // Charger les années disponibles
  function loadYears() {
    const currentYear = new Date().getFullYear();
    const startYear = currentYear - 5;
    const endYear = currentYear + 1;

    const anneeDebutSelect = document.getElementById('annee_debut');
    const anneeFinSelect = document.getElementById('annee_fin');

    for (let year = startYear; year <= endYear; year++) {
      const option1 = document.createElement('option');
      option1.value = year;
      option1.textContent = year;
      anneeDebutSelect.appendChild(option1);

      const option2 = document.createElement('option');
      option2.value = year;
      option2.textContent = year;
      anneeFinSelect.appendChild(option2);
    }
  }

  // Charger les données d'intérêts
  function loadInteretsData() {
    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams(formData);

    // Charger les statistiques
    fetch(API_BASE + `interets/statistiques?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
              updateStatistics(data);
              updateTable(data.donnees);
            })
            .catch(error => {
              console.error('Erreur lors du chargement des statistiques:', error);
              showMessage('Erreur lors du chargement des statistiques', 'error');
            });

    // Charger les données du graphique
    fetch(API_BASE + `interets/chart-data?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
              updateChart(data);
            })
            .catch(error => {
              console.error('Erreur lors du chargement du graphique:', error);
              showMessage('Erreur lors du chargement du graphique', 'error');
            });
  }

  // Mettre à jour les statistiques
  function updateStatistics(stats) {
    document.getElementById('totalInterets').textContent = formatCurrency(stats.total_interets);
    document.getElementById('moyenneInterets').textContent = formatCurrency(stats.moyenne_interets);
    document.getElementById('maxInterets').textContent = formatCurrency(stats.max_interets);
    document.getElementById('nombrePeriodes').textContent = stats.nombre_periodes;
    document.getElementById('totalCapital').textContent = formatCurrency(stats.total_capital || 0);
    document.getElementById('totalPretsActifs').textContent = stats.total_prets_actifs || 0;
  }

  // Mettre à jour le tableau
  function updateTable(donnees) {
    const tbody = document.getElementById('interetsTableBody');
    tbody.innerHTML = '';

    if (donnees && donnees.length > 0) {
      donnees.forEach(function(interet) {
        const row = document.createElement('tr');
        row.innerHTML = `
                        <td>${interet.etablissement_nom}</td>
                        <td>${interet.annee}-${String(interet.mois).padStart(2, '0')}</td>
                        <td>${interet.annee}</td>
                        <td>${getMonthName(interet.mois)}</td>
                        <td>${formatCurrency(interet.montant_interets)}</td>
                        <td>${interet.nombre_prets_actifs}</td>
                        <td>${formatCurrency(interet.capital_total)}</td>
                        <td>${formatDate(interet.date_calcul)}</td>
                    `;
        tbody.appendChild(row);
      });
    } else {
      const row = document.createElement('tr');
      row.innerHTML = '<td colspan="8">Aucune donnée disponible</td>';
      tbody.appendChild(row);
    }
  }

  // Mettre à jour le graphique
  function updateChart(chartData) {
    console.log('Données reçues pour le graphique:', chartData);

    const ctx = document.getElementById('interetsChart').getContext('2d');

    if (interetsChart) {
      interetsChart.destroy();
    }

    // Vérifier si nous avons des données
    if (!chartData || !chartData.labels || !chartData.data || chartData.labels.length === 0) {
      console.log('Aucune donnée pour le graphique');
      return;
    }

    interetsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: chartData.labels,
        datasets: [{
          label: 'Intérêts Mensuels (€)',
          data: chartData.data,
          borderColor: 'rgb(75, 192, 192)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          tension: 0.1,
          fill: true,
          pointBackgroundColor: 'rgb(75, 192, 192)',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return formatCurrency(value);
              }
            }
          },
          x: {
            title: {
              display: true,
              text: 'Période (Année-Mois)'
            }
          }
        },
        plugins: {
          title: {
            display: true,
            text: 'Évolution des Intérêts Mensuels'
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return `Intérêts: ${formatCurrency(context.parsed.y)}`;
              }
            }
          }
        }
      }
    });
  }

  // Calculer les intérêts pour le mois en cours
  function calculerInterets() {
    const currentDate = new Date();
    const data = {
      annee: currentDate.getFullYear(),
      mois: currentDate.getMonth() + 1
    };

    fetch(API_BASE + 'interets/calculer', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
            .then(response => response.json())
            .then(data => {
              if (data.error) {
                showMessage('Erreur: ' + data.error, 'error');
              } else {
                showMessage('Intérêts calculés avec succès!', 'success');
                loadInteretsData();
              }
            })
            .catch(error => {
              console.error('Erreur lors du calcul des intérêts:', error);
              showMessage('Erreur lors du calcul des intérêts', 'error');
            });
  }

  // Fonctions utilitaires
  function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-FR', {
      style: 'currency',
      currency: 'EUR'
    }).format(amount);
  }

  function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR');
  }

  function getMonthName(monthNumber) {
    const months = [
      'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
      'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    ];
    return months[monthNumber - 1];
  }
</script>
</body>
</html>
