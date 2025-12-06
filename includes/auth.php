<?php
session_start();

// Redirige vers login si non connecté
function require_login()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

// Redirige si rôle ne correspond pas
function require_role($role)
{
    require_login();
    if ($_SESSION['role'] !== $role) {
        die("Accès refusé : rôle requis '$role'.");
    }
}

// Redirection utilitaire
function redirect($url)
{
    header("Location: " . $url);
    exit();
}

// Fonction pour obtenir l'utilisateur courant
function current_user($pdo)
{
    if (!isset($_SESSION['user_id']))
        return null;
    $stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}
?>