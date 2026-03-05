<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/Classement.php";

$classementModel = new Classement();
$topSubmissions  = $classementModel->getTopSubmissions(10);
$topUsers        = $classementModel->getTopUsers(10);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Classement – ChallengeHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body class="bg-light">
<?php include 'partials/navbar.php'; ?>

<div class="container py-4">
    <h1 class="fw-bold mb-1">🏆 Classement</h1>
    <p class="text-muted mb-4">Les meilleures participations et contributeurs de ChallengeHub</p>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="classementTab">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#submissions">
                🏅 Meilleures participations
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#users">
                👑 Meilleurs utilisateurs
            </button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- Top Participations -->
        <div class="tab-pane fade show active" id="submissions">
            <div class="card shadow">
                <div class="card-header bg-white fw-bold">Top 10 participations</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Participation</th>
                                <th>Défi</th>
                                <th>Auteur</th>
                                <th>❤️ Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topSubmissions)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">Aucune participation.</td></tr>
                            <?php else: ?>
                                <?php foreach ($topSubmissions as $i => $s): ?>
                                    <tr>
                                        <td class="fw-bold fs-5">
                                            <?php if ($i == 0) echo '🥇';
                                            elseif ($i == 1) echo '🥈';
                                            elseif ($i == 2) echo '🥉';
                                            else echo '#' . ($i+1); ?>
                                        </td>
                                        <td>
                                            <a href="submission_detaile.php?id=<?= $s['id'] ?>&challenge_id=<?= $s['challenge_id'] ?>"
                                               class="text-decoration-none">
                                                <?= htmlspecialchars(substr($s['description'], 0, 60)) ?>...
                                            </a>
                                        </td>
                                        <td>
                                            <a href="challenge_detaille.php?id=<?= $s['challenge_id'] ?>"
                                               class="text-decoration-none">
                                                <?= htmlspecialchars($s['challenge_title']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($s['author_name']) ?></td>
                                        <td><span class="badge bg-danger">❤️ <?= $s['vote_count'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Utilisateurs -->
        <div class="tab-pane fade" id="users">
            <div class="card shadow">
                <div class="card-header bg-white fw-bold">Top 10 utilisateurs</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Utilisateur</th>
                                <th>Participations</th>
                                <th>❤️ Votes reçus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topUsers)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Aucun utilisateur.</td></tr>
                            <?php else: ?>
                                <?php foreach ($topUsers as $i => $u): ?>
                                    <tr>
                                        <td class="fw-bold fs-5">
                                            <?php if ($i == 0) echo '🥇';
                                            elseif ($i == 1) echo '🥈';
                                            elseif ($i == 2) echo '🥉';
                                            else echo '#' . ($i+1); ?>
                                        </td>
                                        <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                                        <td><span class="badge bg-primary"><?= $u['total_submissions'] ?></span></td>
                                        <td><span class="badge bg-danger">❤️ <?= $u['total_votes'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>