<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;

// Détection automatique de la racine du projet
$script_path = $_SERVER['SCRIPT_NAME'];
$project_folder = '/' . trim(dirname($script_path), '/');

// Nettoyer pour gérer les sous-dossiers
if (strpos($script_path, '/freelance/') !== false || strpos($script_path, '/entreprise/') !== false) {
    $project_root = dirname(dirname($script_path));
} else {
    $project_root = dirname($script_path);
}
$project_root = rtrim($project_root, '/') ?: '/';
?>
<header>
    <div class="container">
        <nav class="navbar">
            <a href="<?= $project_root ?>/index.php" class="logo">Freelance<span>Pro</span></a>
            
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
            
            <ul class="nav-links">
                <li><a href="<?= $project_root ?>/index.php#about"><i class="fas fa-info-circle"></i> À propos</a></li>
                <li><a href="<?= $project_root ?>/index.php#services"><i class="fas fa-cogs"></i> Nos services</a></li>
                <li><a href="<?= $project_root ?>/index.php#contact"><i class="fas fa-envelope"></i> Contact</a></li>
                
                <?php if ($is_logged_in): ?>
                    <?php if ($role === 'entreprise'): ?>
                        <li><a href="<?= $project_root ?>/entreprise/dashboard.php" class="btn"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                    <?php else: ?>
                        <li><a href="<?= $project_root ?>/freelance/candidatures.php" class="btn"><i class="fas fa-briefcase"></i> Mes candidatures</a></li>
                    <?php endif; ?>
                    <li><a href="<?= $project_root ?>/logout.php" class="btn btn-secondary"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="<?= $project_root ?>/login.php" class="btn"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mobileBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');

    if (mobileBtn && navLinks) {
        mobileBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
            });
        });
    }
});
</script>
<?php include 'config.php'; ?>