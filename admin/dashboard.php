<?php
// dashboard.php - NE PAS inclure config.php ici

// Auth.php inclut déjà config.php
include '../includes/auth.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// GESTION DE LA DÉCONNEXION
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../index.php');
    exit();
}

include '../includes/db.php';

$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM Utilisateur")->fetchColumn(),
    'offres' => $pdo->query("SELECT COUNT(*) FROM Offre")->fetchColumn(),
    'candidatures' => $pdo->query("SELECT COUNT(*) FROM Candidature")->fetchColumn(),
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Admin - FreelancePro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-navbar {
            background: linear-gradient(135deg, var(--primary), #6a11cb);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .admin-navbar .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .admin-navbar .nav-brand {
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .admin-navbar .nav-brand a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .admin-navbar .nav-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .admin-navbar .nav-link {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .admin-navbar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .admin-navbar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
        }
        .admin-navbar .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .admin-navbar .logout-btn:hover {
            background: rgba(231, 76, 60, 0.8);
        }
    </style>
</head>

<body>
    <!-- Barre de navigation admin -->
    <nav class="admin-navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="dashboard.php">
                    <i class="fas fa-shield-alt"></i>
                    <span>Admin Panel</span>
                </a>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="utilisateurs.php" class="nav-link">
                    <i class="fas fa-users"></i> Utilisateurs
                </a>
                <a href="offres.php" class="nav-link">
                    <i class="fas fa-briefcase"></i> Offres
                </a>
                <a href="candidatures.php" class="nav-link">
                    <i class="fas fa-file-signature"></i> Candidatures
                </a>
                <a href="?logout" class="logout-btn" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <div class="container" style="padding-top: 120px; padding-bottom: 60px;">
        <div class="page-header">
            <h1><i class="fas fa-shield-alt"></i> Tableau de bord administrateur</h1>
            <p class="subtitle">Statistiques et gestion de la plateforme.</p>
        </div>

        <!-- ✅ Utilisation de .stats-cards + .card -->
        <div class="stats-cards">
            <div class="card">
                <h2><?= htmlspecialchars($stats['users']) ?></h2>
                <p>Utilisateurs</p>
            </div>
            <div class="card">
                <h2><?= htmlspecialchars($stats['offres']) ?></h2>
                <p>Offres publiées</p>
            </div>
            <div class="card">
                <h2><?= htmlspecialchars($stats['candidatures']) ?></h2>
                <p>Candidatures</p>
            </div>
        </div>

        <h2 style="color: var(--primary); margin: 40px 0 20px;">Actions rapides</h2>
        <div class="stats-cards">
            <a href="utilisateurs.php" class="card" style="text-decoration: none; color: var(--dark-text);">
                <i class="fas fa-users" style="font-size: 2.5rem; color: var(--accent); margin-bottom: 15px;"></i>
                <h3>Gérer les utilisateurs</h3>
            </a>
            <a href="offres.php" class="card" style="text-decoration: none; color: var(--dark-text);">
                <i class="fas fa-briefcase" style="font-size: 2.5rem; color: var(--accent); margin-bottom: 15px;"></i>
                <h3>Voir toutes les offres</h3>
            </a>
            <a href="candidatures.php" class="card" style="text-decoration: none; color: var(--dark-text);">
                <i class="fas fa-file-signature"
                    style="font-size: 2.5rem; color: var(--accent); margin-bottom: 15px;"></i>
                <h3>Voir toutes les candidatures</h3>
            </a>
        </div>
    </div>

</body>
</html>