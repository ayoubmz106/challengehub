<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/Challenge.php";
require_once __DIR__ . "/../models/Submission.php";
require_once __DIR__ . "/../../config/csrf.php";

$id = (int)($_GET['id'] ?? 0);
$challengeModel  = new Challenge();
$submissionModel = new Submission();

$c           = $challengeModel->getById($id);
$submissions = $submissionModel->getByChallengeId($id);

if (!$c) {
    header("Location: challenge.php");
    exit();
}

$alreadyParticipated = !empty($_SESSION['user_id']) && $submissionModel->hasParticipated($id, $_SESSION['user_id']);

$base   = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$folder = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl = $base . $folder;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($c['title']) ?> – ChallengeHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body class="bg-light">
<?php include 'partials/navbar.php'; ?>

<div class="container py-4">

    <!-- Détail du défi -->
    <div class="card shadow mb-4">
        <?php if ($c['image']): ?>
            <img src="../../public/images/challenges/<?= htmlspecialchars($c['image']) ?>"
                 class="card-img-top challenge-img" alt="image défi">
        <?php endif; ?>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <span class="badge bg-info text-dark mb-2"><?= htmlspecialchars($c['category']) ?></span>
                    <h2 class="fw-bold"><?= htmlspecialchars($c['title']) ?></h2>
                    <p class="text-muted">
                        Par <strong><?= htmlspecialchars($c['author_name']) ?></strong>
                        · 📅 Deadline : <strong><?= htmlspecialchars($c['deadline']) ?></strong>
                    </p>
                </div>
                <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']): ?>
                    <div class="d-flex gap-2">
                        <a href="edit_challenge.php?id=<?= $c['id'] ?>" class="btn btn-outline-secondary btn-sm">✏️ Modifier</a>
                        <form method="POST" action="<?= $baseUrl ?>/app/controllers/ChallengeController.php"
                              onsubmit="return confirm('Supprimer ce défi ?')">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">🗑️ Supprimer</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            <p class="mt-3"><?= nl2br(htmlspecialchars($c['description'])) ?></p>
        </div>
    </div>

    <!-- Participations -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">👥 Participations (<?= count($submissions) ?>)</h4>
        <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] != $c['user_id'] && !$alreadyParticipated): ?>
            <a href="create_submission.php?challenge_id=<?= $c['id'] ?>" class="btn btn-success">+ Participer</a>
        <?php elseif ($alreadyParticipated): ?>
            <span class="badge bg-success fs-6">✅ Vous avez participé</span>
        <?php endif; ?>
    </div>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_GET['success'] === 'submitted' ? 'Participation soumise !' :
               ($_GET['success'] === 'updated' ? 'Participation modifiée !' : 'Participation supprimée.') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($submissions)): ?>
        <div class="alert alert-info">Aucune participation pour l'instant. Soyez le premier !</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($submissions as $s): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm challenge-card">
                        <?php if (!empty($s['image'])): ?>
                            <img src="../../public/images/submissions/<?= htmlspecialchars($s['image']) ?>"
                                 class="card-img-top submission-img" alt="submission">
                        <?php endif; ?>
                        <div class="card-body">
                            <h6 class="fw-bold">
                                <a href="submission_detaile.php?id=<?= $s['id'] ?>&challenge_id=<?= $c['id'] ?>"
                                   class="text-decoration-none text-dark">
                                    👤 <?= htmlspecialchars($s['author_name']) ?>
                                </a>
                            </h6>
                            <p class="text-muted small"><?= htmlspecialchars(substr($s['description'], 0, 100)) ?>...</p>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <small class="text-muted">❤️ <?= $s['vote_count'] ?> vote(s)</small>
                            <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $s['user_id']): ?>
                                <div class="d-flex gap-1">
                                    <a href="edit_submission.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-secondary">✏️</a>
                                    <form method="POST" action="<?= $baseUrl ?>/app/controllers/SubmissionController.php"
                                          onsubmit="return confirm('Supprimer ?')">
                                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <input type="hidden" name="challenge_id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">🗑️</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-3">
        <a href="challenge.php" class="btn btn-outline-secondary">← Retour aux défis</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>