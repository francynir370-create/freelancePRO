<?php
include '../includes/auth.php';
if ($_SESSION['role'] !== 'admin') die("Accès réservé à l'administrateur.");
include '../includes/db.php';

// GESTION DE LA DÉCONNEXION
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../index.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT o.*, e.nom_entreprise, u.email as email_entreprise
    FROM Offre o
    JOIN Entreprise e ON o.id_entreprise = e.id_utilisateur
    JOIN Utilisateur u ON e.id_utilisateur = u.id
    ORDER BY o.date_publication DESC
");
$stmt->execute();
$offres = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Offres - Admin</title>
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
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="utilisateurs.php" class="nav-link">
                    <i class="fas fa-users"></i> Utilisateurs
                </a>
                <a href="offres.php" class="nav-link active">
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
            <h1><i class="fas fa-briefcase"></i> Toutes les offres</h1>
            <p class="subtitle">Surveillance de toutes les missions publiées sur la plateforme.</p>
        </div>

        <?php if (empty($offres)): ?>
            <div class="empty-state">
                <i class="fas fa-briefcase"></i>
                <h3>Aucune offre</h3>
            </div>
        <?php else: ?>
            <?php foreach ($offres as $o): ?>
            <div class="card offre-item">
                <div class="offre-info">
                    <h3 class="card-title"><?= htmlspecialchars($o['titre']) ?></h3>
                    <p><strong>Entreprise :</strong> <?= htmlspecialchars($o['nom_entreprise']) ?> (<?= htmlspecialchars($o['email_entreprise']) ?>)</p>
                    <p><?= htmlspecialchars(substr($o['description'], 0, 200)) ?>...</p>
                    <div class="offre-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <?= date('d/m/Y', strtotime($o['date_publication'])) ?>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-tag"></i>
                            <span class="badge <?= $o['statut'] === 'ouvert' ? 'acceptee' : 'refusee' ?>">
                                <?= $o['statut'] === 'ouvert' ? 'Ouverte' : 'Fermée' ?>
                            </span>
                        </div>
                        <?php if ($o['budget_min'] > 0): ?>
                            <div class="meta-item">
                                <i class="fas fa-euro-sign"></i>
                                <?= number_format($o['budget_min'], 0, ',', ' ') ?> €
                                <?php if ($o['budget_max'] > 0): ?> - <?= number_format($o['budget_max'], 0, ',', ' ') ?> €<?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="offre-actions">
                    <a href="candidatures.php?offre_id=<?= (int)$o['id'] ?>" class="btn" style="background: var(--accent); color: var(--primary);">Voir les candidatures</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>