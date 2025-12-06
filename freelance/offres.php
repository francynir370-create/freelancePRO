<?php
include '../includes/auth.php';
require_role('freelance');
include '../includes/db.php';

$stmt = $pdo->prepare("
    SELECT o.*, e.nom_entreprise
    FROM Offre o
    JOIN Entreprise e ON o.id_entreprise = e.id_utilisateur
    WHERE o.statut = 'ouvert'
    ORDER BY o.date_publication DESC
");
$stmt->execute();
$offres = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Offres disponibles - FreelancePro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container" style="padding-top: 120px; padding-bottom: 60px;">
        <div class="page-header">
            <h1>Offres de mission disponibles</h1>
            <p class="subtitle">Parcourez les projets et postulez dès aujourd'hui.</p>
        </div>

        <?php if (empty($offres)): ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>Aucune offre pour le moment</h3>
                <p>Revenez bientôt !</p>
            </div>
        <?php else: ?>
            <?php foreach ($offres as $o): ?>
            <div class="card offre-item">
                <div class="offre-info">
                    <h3 class="card-title"><?= htmlspecialchars($o['titre']) ?></h3>
                    <p><strong><?= htmlspecialchars($o['nom_entreprise']) ?></strong></p>
                    <p><?= htmlspecialchars(substr($o['description'], 0, 200)) ?>...</p>
                    <div class="offre-meta">
                        <?php if ($o['budget_min'] > 0): ?>
                            <div class="meta-item">
                                <i class="fas fa-euro-sign"></i>
                                <?= number_format($o['budget_min'], 0, ',', ' ') ?> €
                                <?php if ($o['budget_max'] > 0): ?> - <?= number_format($o['budget_max'], 0, ',', ' ') ?> €<?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($o['duree_estimee'])): ?>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <?= htmlspecialchars($o['duree_estimee']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="offre-actions">
                    <a href="offre-detail.php?id=<?= $o['id'] ?>" class="btn">Voir les détails</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
<?php include 'includes/footer.php';?>