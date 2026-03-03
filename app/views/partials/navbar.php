<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav style="background:#2d3748;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;">
    <a href="../../index.php" style="color:#fff;font-size:20px;font-weight:bold;text-decoration:none;">🏆 ChallengeHub</a>
    <div style="display:flex;gap:15px;">
        <a href="challenge.php" style="color:#fff;text-decoration:none;">Défis</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="create_challenge.php" style="color:#68d391;text-decoration:none;">+ Créer</a>
            <span style="color:#a0aec0;">Bonjour, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="/challengeshub/app/controllers/UserController.php?action=logout" style="color:#fc8181;text-decoration:none;">Déconnexion</a>
        <?php else: ?>
            <a href="login.php" style="color:#fff;text-decoration:none;">Connexion</a>
            <a href="registre.php" style="color:#68d391;text-decoration:none;">Inscription</a>
        <?php endif; ?>
    </div>
</nav>