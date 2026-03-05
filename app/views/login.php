<?php
session_start();
require_once __DIR__ . "/../../config/csrf.php";
$base   = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$folder = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl = $base . $folder;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion – ChallengeHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body class="bg-light">
<?php include 'partials/navbar.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height:85vh;">
    <div class="card shadow" style="width:100%;max-width:450px;">
        <div class="card-body p-5">
            <h2 class="card-title text-center mb-4">🔐 Connexion</h2>

            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger">Email ou mot de passe incorrect.</div>
            <?php endif; ?>
            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success">Compte créé ! Connectez-vous.</div>
            <?php endif; ?>

            <form method="POST" action="<?= $baseUrl ?>/app/controllers/UserController.php">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="votre@email.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" name="login" value="1" class="btn btn-primary w-100">Se connecter</button>
            </form>
            <p class="text-center mt-3 mb-0">Pas de compte ? <a href="registre.php">S'inscrire</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>