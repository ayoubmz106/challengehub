<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#1a202c;">
    <div class="container">
        <a class="navbar-brand" href="/challengeshub/index.php">🏆 ChallengeHub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item">
                    <a class="nav-link" href="/challengeshub/app/views/challenge.php">Défis</a>
                </li>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link text-success fw-bold" href="/challengeshub/app/views/create_challenge.php">+ Créer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/challengeshub/app/views/classement.php">🏆 Classement</a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link text-warning">👋 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-danger btn-sm" href="/challengeshub/app/controllers/UserController.php?action=logout">Déconnexion</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/challengeshub/app/views/login.php">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-success btn-sm" href="/challengeshub/app/views/registre.php">Inscription</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>