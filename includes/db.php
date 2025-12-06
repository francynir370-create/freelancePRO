<?php
// Connexion à la base de données MySQL
$host = 'localhost';
$dbname = 'freelancepro';
$username = 'root';   // ← Modifiez si nécessaire (ex: votre hébergeur)
$password = '';       // ← Modifiez si nécessaire

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()));
}
?>