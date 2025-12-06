<?php
include '../includes/auth.php';
require_role('entreprise');
include '../includes/db.php';

$stmt = $pdo->prepare("SELECT id, titre, date_publication, statut FROM Offre WHERE id_entreprise = ? ORDER BY date_publication DESC");
$stmt->execute([$_SESSION['user_id']]);
$offres = $stmt->fetchAll();

$candidatures = [];
$offre_active = null;
if (isset($_GET['offre_id'])) {
    $offre_id = (int)$_GET['offre_id'];
    $stmt = $pdo->prepare("SELECT id FROM Offre WHERE id = ? AND id_entreprise = ?");
    $stmt->execute([$offre_id, $_SESSION['user_id']]);
    if (!$stmt->fetch()) die("Accès refusé.");

    $stmt = $pdo->prepare("
        SELECT c.*, u.email, pf.nom, pf.prenom, pf.bio
        FROM Candidature c
        JOIN Utilisateur u ON c.id_freelance = u.id
        JOIN ProfilFreelance pf ON u.id = pf.id_utilisateur
        WHERE c.id_offre = ?
        ORDER BY c.date_candidature DESC
    ");
    $stmt->execute([$offre_id]);
    $candidatures = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT titre FROM Offre WHERE id = ?");
    $stmt->execute([$offre_id]);
    $offre_active = $stmt->fetch()['titre'] ?? 'Offre';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - FreelancePro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container" style="padding-top: 120px; padding-bottom: 60px;">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 30px;">
            <div>
                <h1 style="color: var(--primary); margin: 0;">Tableau de bord</h1>
                <p style="color: var(--gray); margin: 5px 0 0;">Gérez vos offres et candidatures</p>
            </div>
            <a href="publier-offre.php" class="btn" style="background: var(--secondary); color: var(--primary);"><i class="fas fa-bullhorn"></i> Nouvelle offre</a>
        </div>

        <?php if (!$offre_active): ?>
            <div class="page-header"><h2>Mes offres publiées</h2></div>
            <?php if (empty($offres)): ?>
                <div class="empty-state">
                    <i class="fas fa-file-contract"></i>
                    <h3>Pas encore d'offre</h3>
                    <a href="publier-offre.php" class="btn" style="margin-top: 20px; background: var(--secondary); color: var(--primary);">Publier une offre</a>
                </div>
            <?php else: ?>
                <?php foreach ($offres as $offre): ?>
                <div class="card offre-item">
                    <div class="offre-info">
                        <h3 class="card-title"><?= htmlspecialchars($offre['titre']) ?></h3>
                        <div class="offre-meta">
                            <div class="meta-item"><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($offre['date_publication'])) ?></div>
                            <div class="meta-item">
                                <span class="badge <?= $offre['statut'] === 'ouvert' ? 'acceptee' : 'refusee' ?>">
                                    <?= $offre['statut'] === 'ouvert' ? 'Ouverte' : 'Fermée' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="offre-actions">
                        <a href="?offre_id=<?= $offre['id'] ?>" class="btn" style="background: var(--accent); color: var(--primary);">Voir les candidatures</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php else: ?>
            <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour à mes offres</a>
            <h2 style="color: var(--primary); margin: 10px 0;">Candidatures pour : <?= htmlspecialchars($offre_active) ?></h2>
            <?php if (empty($candidatures)): ?>
                <div class="empty-state"><i class="fas fa-user-clock"></i><h3>Aucune candidature</h3></div>
            <?php else: ?>
                <?php foreach ($candidatures as $c): ?>
                <div class="candidature-item">
                    <div class="candidat-name">
                        <?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?>
                        <span class="badge <?= $c['statut'] ?>"><?= ucfirst($c['statut']) ?></span>
                    </div>
                    <p><strong>Email :</strong> <?= htmlspecialchars($c['email']) ?></p>
                    <?php if (!empty($c['bio'])): ?><p><em><?= htmlspecialchars($c['bio']) ?></em></p><?php endif; ?>
                    <div class="candidat-message"><?= nl2br(htmlspecialchars($c['message_motivation'])) ?></div>
                    <p style="font-size: 0.9rem; color: var(--gray);"><i class="fas fa-clock"></i> <?= date('d/m/Y à H:i', strtotime($c['date_candidature'])) ?></p>
                    <?php if ($c['statut'] === 'en_attente'): ?>
                        <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                            <a href="traiter-candidature.php?id=<?= $c['id'] ?>&action=accepter" class="action-btn accept"><i class="fas fa-check"></i> Accepter</a>
                            <a href="traiter-candidature.php?id=<?= $c['id'] ?>&action=refuser" class="action-btn reject"><i class="fas fa-times"></i> Refuser</a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
<?php include '../includes/footer.php';?>