<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/challenge.php";
require_once __DIR__ . "/../models/Submission.php";

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($c['title']) ?></title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container">

    <!-- Détail du défi -->
    <div class="challenge-detail">
        <?php if ($c['image']): ?>
            <img src="../../public/images/challenges/<?= htmlspecialchars($c['image']) ?>" class="challenge-img">
        <?php endif; ?>
        <span class="badge"><?= htmlspecialchars($c['category']) ?></span>
        <h1><?= htmlspecialchars($c['title']) ?></h1>
        <p class="meta">
            Par <strong><?= htmlspecialchars($c['author_name']) ?></strong>
            · Deadline : <strong><?= htmlspecialchars($c['deadline']) ?></strong>
        </p>
        <p><?= nl2br(htmlspecialchars($c['description'])) ?></p>

        <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']): ?>
            <div style="margin-top:15px;display:flex;gap:10px;">
                <a href="edit_challenge.php?id=<?= $c['id'] ?>" class="btn">✏️ Modifier</a>
                <form method="POST" action="/<?= trim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>/app/controllers/ChallengeController.php"
                      style="display:inline" onsubmit="return confirm('Supprimer ?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <hr style="margin:30px 0;">

    <!-- Participations -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h2>Participations (<?= count($submissions) ?>)</h2>
        <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] != $c['user_id'] && !$alreadyParticipated): ?>
            <a href="create_submission.php?challenge_id=<?= $c['id'] ?>" class="btn btn-primary">+ Participer</a>
        <?php elseif ($alreadyParticipated): ?>
            <span class="badge" style="background:#c6f6d5;color:#276749;">✅ Vous avez participé</span>
        <?php endif; ?>
    </div>

    <?php if (!empty($_GET['success'])): ?>
        <p class="alert success">
            <?= $_GET['success'] === 'submitted' ? 'Participation soumise !' :
               ($_GET['success'] === 'updated'   ? 'Participation modifiée !' : 'Participation supprimée.') ?>
        </p>
    <?php endif; ?>
    <?php if (!empty($_GET['error']) && $_GET['error'] === 'already'): ?>
        <p class="alert error">Vous avez déjà participé à ce défi.</p>
    <?php endif; ?>

    <?php if (empty($submissions)): ?>
        <p>Aucune participation pour l'instant. Soyez le premier !</p>
    <?php else: ?>
        <div class="challenges-grid">
            <?php foreach ($submissions as $s): ?>
                <div class="challenge-card">
                    <?php if ($s['image']): ?>
                        <img src="../../public/images/submissions/<?= htmlspecialchars($s['image']) ?>" alt="submission">
                    <?php endif; ?>
                    <p><strong><?= htmlspecialchars($s['author_name']) ?></strong></p>
                    <p><?= nl2br(htmlspecialchars($s['description'])) ?></p>
                    <?php if ($s['link']): ?>
                        <p><a href="<?= htmlspecialchars($s['link']) ?>" target="_blank">🔗 Voir le lien</a></p>
                    <?php endif; ?>
                    <p class="meta">
                        ❤️ <?= $s['vote_count'] ?> vote(s)
                        · <?= htmlspecialchars($s['created_at']) ?>
                    </p>

                    <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $s['user_id']): ?>
                        <div class="card-actions">
                            <a href="edit_submission.php?id=<?= $s['id'] ?>" class="btn btn-sm">✏️ Modifier</a>
                            <form method="POST" action="/<?= trim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>/app/controllers/SubmissionController.php"
                                  style="display:inline" onsubmit="return confirm('Supprimer ?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <input type="hidden" name="challenge_id" value="<?= $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
</body>
</html>