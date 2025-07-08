<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fas fa-university me-2"></i>
                <span class="fw-bold">Système Bancaire Étudiant</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Dropdown Gestion des Prêts -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="pretsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-hand-holding-usd me-1"></i> Gestion des Prêts
</a>
                        <ul class="dropdown-menu" aria-labelledby="pretsDropdown">
                            <li><a class="dropdown-item" href="templates/types-prets.php"><i class="fas fa-list me-2"></i> Types de Prêts</a></li>
                            <li><a class="dropdown-item" href="templates/prets.php"><i class="fas fa-file-contract me-2"></i> Prêts</a></li>
                            <li><a class="dropdown-item" href="templates/approuver_prets.php"><i class="fas fa-check-circle me-2"></i> Approuver Prêts</a></li>
                            <li><a class="dropdown-item" href="templates/simulations.php"><i class="fas fa-calculator me-2"></i> Simulations</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="templates/interets.php"><i class="fas fa-percentage me-2"></i> Intérêts</a></li>
                            <li><a class="dropdown-item" href="templates/montant_disponibles.php"><i class="fas fa-money-check-alt me-2"></i> Montants Disponibles</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Remboursements & Transactions -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="transactionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-exchange-alt me-1"></i> Transactions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="transactionsDropdown">
                            <li><a class="dropdown-item" href="templates/remboursements.php"><i class="fas fa-calendar-check me-2"></i> Remboursements</a></li>
                            <li><a class="dropdown-item" href="templates/transactions.php"><i class="fas fa-receipt me-2"></i> Historique Transactions</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Gestion des Utilisateurs -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="usersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-users me-1"></i> Utilisateurs
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="usersDropdown">
                            <li><a class="dropdown-item" href="templates/etudiants.php"><i class="fas fa-user-graduate me-2"></i> Étudiants</a></li>
                            <li><a class="dropdown-item" href="templates/etablissements.php"><i class="fas fa-building me-2"></i> Établissements</a></li>
                        </ul>
                    </li>

                    <!-- Lien direct API -->
                    <li class="nav-item">
                        <a class="nav-link" href="https://github.com/orgs/Virtual-Vision-Synergy/projects/8"><i class="fab fa-github me-2"></i> TODO LIST</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://github.com/Virtual-Vision-Synergy/P17-ETU003250-ETU003274-ETU003373"><i class="fab fa-github me-2"></i> GITHUB</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
