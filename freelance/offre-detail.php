<?php
include '../includes/auth.php';
require_role('freelance');
include '../includes/db.php';

$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) die("Offre non trouvée.");

$stmt = $pdo->prepare("
    SELECT o.*, e.nom_entreprise
    FROM Offre o
    JOIN Entreprise e ON o.id_entreprise = e.id_utilisateur
    WHERE o.id = ? AND o.statut = 'ouvert'
");
$stmt->execute([$id]);
$offre = $stmt->fetch();
if (!$offre) die("Offre non trouvée.");

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if ($message) {
        $stmt = $pdo->prepare("SELECT id FROM Candidature WHERE id_offre = ? AND id_freelance = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $error = "Vous avez déjà postulé à cette offre.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO Candidature (id_offre, id_freelance, message_motivation) VALUES (?, ?, ?)");
            $stmt->execute([$id, $_SESSION['user_id'], $message]);
            header('Location: candidatures.php');
            exit();
        }
    } else {
        $error = "Le message de motivation est requis.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($offre['titre']) ?> - FreelancePro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container" style="padding-top: 120px; padding-bottom: 60px;">
        <a href="offres.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour aux offres</a>

        <div class="card">
            <h1 class="card-title"><?= htmlspecialchars($offre['titre']) ?></h1>
            <p><strong>Entreprise :</strong> <?= htmlspecialchars($offre['nom_entreprise']) ?></p>
            
            <div class="offre-meta" style="margin: 15px 0;">
                <?php if ($offre['budget_min'] > 0): ?>
                    <div class="meta-item">
                        <i class="fas fa-euro-sign"></i>
                        Budget : <?= number_format($offre['budget_min'], 0, ',', ' ') ?> €
                        <?php if ($offre['budget_max'] > 0): ?> - <?= number_format($offre['budget_max'], 0, ',', ' ') ?> €<?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($offre['duree_estimee'])): ?>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <?= htmlspecialchars($offre['duree_estimee']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="margin: 25px 0; padding: 20px; background: #f9f5ff; border-radius: 16px;">
                <h3 style="color: var(--primary); margin-bottom: 15px;">Description</h3>
                <p style="line-height: 1.7;"><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
            </div>

            <?php if ($error): ?>
                <div class="error" style="margin: 20px 0;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" style="margin-top: 30px;">
                <h3 style="color: var(--primary); margin-bottom: 15px;">Postuler</h3>
                <div class="form-group">
                    <textarea name="message" class="form-control" required placeholder="Décrivez pourquoi vous êtes le/la bon(ne) candidat(e)..."></textarea>
                </div>
                <button type="submit" class="btn" style="width: auto;">Envoyer ma candidature</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php include 'includes/footer.php';?>