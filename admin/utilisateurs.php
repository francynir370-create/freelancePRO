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

// Définir la fonction redirect si elle n'existe pas
if (!function_exists('redirect')) {
    function redirect($url) {
        header('Location: ' . $url);
        exit();
    }
}

// Récupérer tous les utilisateurs
$stmt = $pdo->query("
    SELECT u.*, 
           COALESCE(pf.nom, e.nom_entreprise, '') AS nom_affiche
    FROM Utilisateur u
    LEFT JOIN ProfilFreelance pf ON u.id = pf.id_utilisateur
    LEFT JOIN Entreprise e ON u.id = e.id_utilisateur
    ORDER BY u.date_inscription DESC
");
$utilisateurs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Utilisateurs - Admin</title>
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
                <a href="utilisateurs.php" class="nav-link active">
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
            <h1><i class="fas fa-users"></i> Gestion des utilisateurs</h1>
            <p class="subtitle">Liste complète des freelances, entreprises et administrateurs.</p>
        </div>

        <?php if (empty($utilisateurs)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>Aucun utilisateur</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 6px 18px rgba(75,0,130,0.08);">
                    <thead style="background: var(--primary); color: white;">
                        <tr>
                            <th style="padding: 16px; text-align: left;">ID</th>
                            <th style="padding: 16px; text-align: left;">Nom / Entreprise</th>
                            <th style="padding: 16px; text-align: left;">Email</th>
                            <th style="padding: 16px; text-align: left;">Rôle</th>
                            <th style="padding: 16px; text-align: left;">Inscription</th>
                            <th style="padding: 16px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $u): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 16px;"><?= htmlspecialchars($u['id']) ?></td>
                            <td style="padding: 16px;"><?= htmlspecialchars($u['nom_affiche'] ?: 'Non renseigné') ?></td>
                            <td style="padding: 16px;"><?= htmlspecialchars($u['email']) ?></td>
                            <td style="padding: 16px;">
                                <span class="badge <?= $u['role'] === 'admin' ? 'acceptee' : ($u['role'] === 'entreprise' ? 'attente' : 'refusee') ?>">
                                    <?= ucfirst(htmlspecialchars($u['role'])) ?>
                                </span>
                            </td>
                            <td style="padding: 16px;"><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
                            <td style="padding: 16px; text-align: center;">
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($u['id']) ?>">
                                    <button type="submit" name="supprimer" class="action-btn reject" style="padding: 6px 12px; font-size: 0.85rem;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>

<?php
// Suppression d'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
    $id = (int)$_POST['id'];
    
    // Empêche l'auto-suppression
    if ($id !== $_SESSION['user_id'] && $id > 0) {
        // Supprimer en cascade si nécessaire
        $pdo->prepare("DELETE FROM Utilisateur WHERE id = ?")->execute([$id]);
        redirect('utilisateurs.php');
    }
}
?>