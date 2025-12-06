<?php
include '../includes/auth.php';
require_role('entreprise');
include '../includes/db.php';

$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
$action = $_GET['action'] ?? '';
if (!$id || !in_array($action, ['accepter', 'refuser'])) die("Requête invalide.");

$stmt = $pdo->prepare("SELECT c.id FROM Candidature c JOIN Offre o ON c.id_offre = o.id WHERE c.id = ? AND o.id_entreprise = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
if (!$stmt->fetch()) die("Accès refusé.");

$statut = ($action === 'accepter') ? 'acceptee' : 'refusee';
$stmt = $pdo->prepare("UPDATE Candidature SET statut = ? WHERE id = ?");
$stmt->execute([$statut, $id]);

header('Location: dashboard.php' . (isset($_GET['offre_id']) ? '?offre_id=' . $_GET['offre_id'] : ''));
exit();
?>
<?php include 'includes/footer.php';?>