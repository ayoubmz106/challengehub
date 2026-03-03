<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription – ChallengeHub</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="form-container">
    <h2>Inscription</h2>

    <?php if (!empty($_GET['error'])): ?>
        <p class="alert error">
            <?= $_GET['error'] === 'exists' ? 'Email déjà utilisé.' : 'Remplissez tous les champs.' ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="../../app/controllers/UserController.php">
        <input type="text" name="nom" placeholder="Nom complet" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit" name="register" value="1">S'inscrire</button>
    </form>
    <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
</div>
</body>
</html>