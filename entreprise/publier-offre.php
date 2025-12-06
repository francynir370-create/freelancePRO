<?php
include '../includes/auth.php';
require_role('entreprise');
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $budget_min = floatval($_POST['budget_min'] ?? 0);
    $budget_max = floatval($_POST['budget_max'] ?? 0);
    $duree = trim($_POST['duree_estimee'] ?? '');

    if ($titre && $desc) {
        $stmt = $pdo->prepare("INSERT INTO Offre (id_entreprise, titre, description, budget_min, budget_max, duree_estimee, statut) VALUES (?, ?, ?, ?, ?, ?, 'ouvert')");
        $stmt->execute([$_SESSION['user_id'], $titre, $desc, $budget_min, $budget_max, $duree]);
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Publier une offre - FreelancePro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container" style="padding-top: 90px; padding-bottom: 60px;">
        <div class="form-card">
            <div class="form-header">
                <h2><i class="fas fa-bullhorn" style="color: var(--accent);"></i> Publier une nouvelle offre</h2>
                <p>Décrivez votre mission en quelques mots.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error" style="margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Titre de la mission <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="titre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description détaillée <span style="color: var(--danger);">*</span></label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Budget minimum (€)</label>
                        <input type="number" name="budget_min" class="form-control" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Budget maximum (€)</label>
                        <input type="number" name="budget_max" class="form-control" min="0" step="0.01">
                    </div>
                </div>
                <div class="form-group">
                    <label>Durée estimée</label>
                    <input type="text" name="duree_estimee" class="form-control" placeholder="Ex: 3 mois">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn" style="background: var(--primary);"><i class="fas fa-paper-plane"></i> Publier</button>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                </div>
            </form>
            <div class="form-note"><p><span style="color: var(--danger);">*</span> Champs obligatoires</p></div>
        </div>
    </div>
</body>
</html>
<?php include '../includes/footer.php';?>