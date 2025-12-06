<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'includes/db.php';
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (!in_array($role, ['freelance', 'entreprise'])) {
        $error = "Rôle invalide.";
    } elseif ($email && $password && strlen($password) >= 6) {
        // Vérifier si email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM Utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->beginTransaction();
            try {
                // Insérer utilisateur
                $stmt = $pdo->prepare("INSERT INTO Utilisateur (email, mot_de_passe, role) VALUES (?, ?, ?)");
                $stmt->execute([$email, $hash, $role]);

                $user_id = $pdo->lastInsertId();

                // Insérer profil vide
                if ($role === 'freelance') {
                    $pdo->prepare("INSERT INTO ProfilFreelance (id_utilisateur, nom, prenom) VALUES (?, '', '')")->execute([$user_id]);
                } else {
                    $pdo->prepare("INSERT INTO Entreprise (id_utilisateur, nom_entreprise) VALUES (?, '')")->execute([$user_id]);
                }

                $pdo->commit();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role;
                header('Location: index.php');
                exit();
            } catch (Exception $e) {
                $pdo->rollback();
                $error = "Erreur lors de l'inscription.";
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs correctement (mot de passe ≥ 6 caractères).";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription - FreelancePro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .error {
            color: #e74c3c;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2 style="text-align:center;margin-bottom:30px;">Créer un compte</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email professionnel</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe (min. 6 caractères)</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Je suis :</label>
                <select id="role" name="role" required>
                    <option value="">-- Sélectionnez --</option>
                    <option value="freelance">Freelance (je cherche des missions)</option>
                    <option value="entreprise">Entreprise (je cherche des talents)</option>
                </select>
            </div>
            <button type="submit" class="btn" style="width:100%;">S'inscrire</button>
        </form>
        <p style="text-align:center;margin-top:20px;">
            Déjà inscrit ? <a href="login.php">Connectez-vous</a>
        </p>
    </div>
</body>

</html>