<?php
include '../includes/auth.php';
require_role('freelance');
include '../includes/db.php';
include '../includes/navbar.php'; 

// Récupérer les candidatures de l'utilisateur
$stmt = $pdo->prepare("
    SELECT c.*, o.titre, o.description, e.nom_entreprise
    FROM Candidature c
    JOIN Offre o ON c.id_offre = o.id
    JOIN Entreprise e ON o.id_entreprise = e.id_utilisateur
    WHERE c.id_freelance = ?
    ORDER BY c.date_candidature DESC
");
$stmt->execute([$_SESSION['user_id']]);
$candidatures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes candidatures - FreelancePro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard {
            max-width: 1000px;
            margin: 80px auto;
            padding: 0 20px;
        }
        .candidature-card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .statut {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        .statut.en_attente { background: #fff3cd; color: #856404; }
        .statut.acceptee { background: #d4edda; color: #155724; }
        .statut.refusee { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="dashboard">
        <h2>Mes candidatures</h2>
        <?php if (empty($candidatures)): ?>
            <p>Vous n'avez pas encore postulé à d'offres. <a href="offres.php">Voir les offres disponibles</a>.</p>
        <?php else: ?>
            <?php foreach ($candidatures as $c): ?>
            <div class="candidature-card">
                <h3><?= htmlspecialchars($c['titre']) ?></h3>
                <p><strong>Entreprise :</strong> <?= htmlspecialchars($c['nom_entreprise']) ?></p>
                <p><?= htmlspecialchars(substr($c['description'], 0, 200)) ?>...</p>
                <p><strong>Statut :</strong> <span class="statut <?= $c['statut'] ?>"><?= ucfirst($c['statut']) ?></span></p>
                <small>Postulé le : <?= date('d/m/Y à H:i', strtotime($c['date_candidature'])) ?></small>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="offres.php" class="btn" style="margin-top:20px;">Voir toutes les offres</a>
    </div>
</body>
</html>
<?php include 'includes/footer.php';?>