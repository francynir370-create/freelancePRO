<?php include 'includes/auth.php';?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreelancePro | Trouvez des talents, décrochez des projets</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Navigation -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">Freelance<span>Pro</span></a>
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
                <ul class="nav-links">
                    <li><a href="#about"><i class="fas fa-info-circle"></i> À propos</a></li>
                    <li><a href="#services"><i class="fas fa-cogs"></i> Nos services</a></li>
                    <li><a href="#contact"><i class="fas fa-envelope"></i> Contact</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] === 'entreprise'): ?>
                            <li><a href="entreprise/publier-offre.php" class="btn"><i class="fas fa-bullhorn"></i> Publier une
                                    offre</a></li>
                        <?php else: ?>
                            <li><a href="freelance/candidatures.php" class="btn"><i class="fas fa-briefcase"></i> Mes
                                    candidatures</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php" class="btn btn-secondary"><i class="fas fa-sign-out-alt"></i>
                                Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Le reste de votre HTML exactement tel quel (section hero, about, services, footer) -->
    <!-- ⚠️ Supprimez le <script> en fin de body → remplacé par main.js -->

    <!-- Section Hero -->
    <section class="hero">
        <div class="container">
            <h1>BIENVENUE CHEZ <span>FREELANCEPRO</span></h1>
            <p>Là où les talents exceptionnels et les projets ambitieux se rencontrent. Rejoignez notre communauté de
                plus de 10 000 freelances et entreprises.</p>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="hero-btns">
                    <a href="login.php" class="btn"><i class="fas fa-bullhorn"></i> Publier une offre</a>
                    <a href="login.php" class="btn btn-secondary"><i class="fas fa-briefcase"></i> Postuler maintenant</a>
                </div>
            <?php endif; ?>

            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number">2,500+</span>
                    <span class="stat-text">Projets réalisés</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">10,000+</span>
                    <span class="stat-text">Freelances actifs</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">4.9/5</span>
                    <span class="stat-text">Satisfaction client</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">95%</span>
                    <span class="stat-text">Projets livrés à temps</span>
                </div>
            </div>
        </div>
    </section>

    <!-- À propos -->
    <section id="about" class="section about">
        <div class="container">
            <div class="section-title">
                <h2>Qui sommes-nous ?</h2>
                <p>Découvrez la plateforme qui révolutionne le freelancing en connectant les talents aux opportunités
                </p>
            </div>
            <div class="about-content">
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                        alt="Équipe FreelancePro">
                </div>
                <div class="about-text">
                    <h3>Votre partenaire de confiance en freelancing</h3>
                    <p>Nous sommes une plateforme spécialisée...</p>
                    <!-- ... (gardez tout le contenu HTML original ici) ... -->
                    <a href="#contact" class="btn" style="margin-top: 20px;">Nous contacter</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section id="services" class="section services">
        <div class="container">
            <div class="section-title">
                <h2>Nos services</h2>
                <p>Des solutions adaptées pour les entreprises et les freelances</p>
            </div>
            <div class="services-cards">
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-building"></i></div>
                    <h3>Pour les entreprises</h3>
                    <p>Publiez vos offres et trouvez le talent parfait...</p>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'entreprise'): ?>
                        <li><a href="entreprise/dashboard.php" class="btn"><i class="fas fa-tachometer-alt"></i> Tableau de
                                bord</a></li>
                    <?php else: ?>
                        <a href="login.php" class="btn">Publier une offre</a>
                        <p class="login-notice" style="margin-top:10px;font-size:0.9rem;color:#6c757d;">* Connexion requise
                        </p>
                    <?php endif; ?>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-user-tie"></i></div>
                    <h3>Pour les freelances</h3>
                    <p>Trouvez des missions passionnantes...</p>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'freelance'): ?>
                        <a href="freelance/offres.php" class="btn btn-secondary">Voir les offres</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-secondary">Postuler maintenant</a>
                        <p class="login-notice" style="margin-top:10px;font-size:0.9rem;color:#6c757d;">* Connexion requise
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Catégories -->
            <div style="text-align:center;margin-top:60px;">
                <h3 style="margin-bottom:30px;">Domaines les plus demandés</h3>
                <div style="display:flex;justify-content:center;flex-wrap:wrap;gap:15px;">
                    <span style="background:#e9ecef;padding:10px 20px;border-radius:30px;">Développement Web</span>
                    <span style="background:#e9ecef;padding:10px 20px;border-radius:30px;">Design Graphique</span>
                    <span style="background:#e9ecef;padding:10px 20px;border-radius:30px;">Rédaction SEO</span>
                    <span style="background:#e9ecef;padding:10px 20px;border-radius:30px;">Marketing Digital</span>
                    <span style="background:#e9ecef;padding:10px 20px;border-radius:30px;">Montage Vidéo</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <!-- ... (gardez tout le footer HTML original) ... -->
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>

</html>
<?php include 'includes/footer.php';?>