<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion – ChallengeHub</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="form-container">
    <h2>Connexion</h2>

    <?php if (!empty($_GET['error'])): ?>
        <p class="alert error">Email ou mot de passe incorrect.</p>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
        <p class="alert success">Compte créé ! Connectez-vous.</p>
    <?php endif; ?>

    <form method="POST" action="../../app/controllers/UserController.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit" name="login" value="1">Se connecter</button>
    </form>
    <p>Pas de compte ? <a href="registre.php">S'inscrire</a></p>
</div>
</body>
</html>