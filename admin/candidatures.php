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

// Récupérer l'offre sélectionnée (optionnel)
$offre_id = isset($_GET['offre_id']) ? (int)$_GET['offre_id'] : null;

if ($offre_id) {
    $stmt = $pdo->prepare("
        SELECT c.*, 
               o.titre as offre_titre,
               e.nom_entreprise,
               u_entreprise.email as email_entreprise,
               u_freelance.email as email_freelance,
               pf.prenom, pf.nom, pf.bio
        FROM Candidature c
        JOIN Offre o ON c.id_offre = o.id
        JOIN Entreprise e ON o.id_entreprise = e.id_utilisateur
        JOIN Utilisateur u_entreprise ON e.id_utilisateur = u_entreprise.id
        JOIN Utilisateur u_freelance ON c.id_freelance = u_freelance.id
        LEFT JOIN ProfilFreelance pf ON u_freelance.id = pf.id_utilisateur
        WHERE c.id_offre = ?
        ORDER BY c.date_candidature DESC
    ");
    $stmt->execute([$offre_id]);
    $candidatures = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare("
        SELECT c.*, 
               o.titre as offre_titre,
               e.nom_entreprise,
               u_entreprise.email as email_entreprise,
               u_freelance.email as email_freelance,
               pf.prenom, pf.nom, pf.bio
        FROM Candidature c
        JOIN Offre o ON c.id_offre = o.id
        JOIN Entreprise e ON o.id_entreprise = e.id_utilisateur
        JOIN Utilisateur u_entreprise ON e.id_utilisateur = u_entreprise.id
        JOIN Utilisateur u_freelance ON c.id_freelance = u_freelance.id
        LEFT JOIN ProfilFreelance pf ON u_freelance.id = pf.id_utilisateur
        ORDER BY c.date_candidature DESC
    ");
    $stmt->execute();
    $candidatures = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Candidatures - Admin</title>
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
                <a href="offres.php" class="nav-link">
                    <i class="fas fa-briefcase"></i> Offres
                </a>
                <a href="candidatures.php" class="nav-link active">
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
            <h1><i class="fas fa-file-signature"></i> Toutes les candidatures</h1>
            <p class="subtitle">Suivi de chaque candidature sur la plateforme.</p>
        </div>

        <?php if (empty($candidatures)): ?>
            <div class="empty-state">
                <i class="fas fa-file-signature"></i>
                <h3>Aucune candidature</h3>
            </div>
        <?php else: ?>
            <?php foreach ($candidatures as $c): ?>
            <div class="card candidature-item">
                <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <div class="candidat-name">
                            <?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?>
                            <span class="badge <?= htmlspecialchars($c['statut']) ?>"><?= ucfirst(htmlspecialchars($c['statut'])) ?></span>
                        </div>
                        <p><strong>Email :</strong> <?= htmlspecialchars($c['email_freelance']) ?></p>
                    </div>
                    <div style="text-align: right;">
                        <p><strong>Offre :</strong> <?= htmlspecialchars($c['offre_titre']) ?></p>
                        <p><strong>Entreprise :</strong> <?= htmlspecialchars($c['nom_entreprise']) ?></p>
                    </div>
                </div>
                <?php if (!empty($c['bio'])): ?>
                    <p><em><?= htmlspecialchars($c['bio']) ?></em></p>
                <?php endif; ?>
                <div class="candidat-message">
                    <?= nl2br(htmlspecialchars($c['message_motivation'])) ?>
                </div>
                <p style="font-size: 0.9rem; color: var(--gray);">
                    <i class="fas fa-clock"></i> <?= date('d/m/Y à H:i', strtotime($c['date_candidature'])) ?>
                </p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>