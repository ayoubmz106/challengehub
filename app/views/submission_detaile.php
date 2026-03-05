<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/Submission.php";
require_once __DIR__ . "/../models/Comment.php";
require_once __DIR__ . "/../models/Vote.php";
require_once __DIR__ . "/../../config/csrf.php";

$id           = (int)($_GET['id'] ?? 0);
$challenge_id = (int)($_GET['challenge_id'] ?? 0);

$submissionModel = new Submission();
$commentModel    = new Comment();
$voteModel       = new Vote();

$sub      = $submissionModel->getById($id);
$comments = $commentModel->getBySubmissionId($id);

if (!$sub) {
    header("Location: challenge.php");
    exit();
}

$voteCount = $voteModel->countVotes($id);
$hasVoted  = !empty($_SESSION['user_id']) && $voteModel->hasVoted($id, $_SESSION['user_id']);

$base   = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$folder = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl = $base . $folder;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Participation – ChallengeHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body class="bg-light">
<?php include 'partials/navbar.php'; ?>

<div class="container py-4">
    <a href="challenge_detaille.php?id=<?= $sub['challenge_id'] ?>" class="btn btn-outline-secondary btn-sm mb-3">← Retour au défi</a>

    <!-- Participation -->
    <div class="card shadow mb-4">
        <?php if ($sub['image']): ?>
            <img src="../../public/images/submissions/<?= htmlspecialchars($sub['image']) ?>"
                 class="card-img-top submission-img" alt="submission">
        <?php endif; ?>
        <div class="card-body">
            <h4 class="fw-bold">Participation de <span class="text-primary"><?= htmlspecialchars($sub['author_name']) ?></span></h4>
            <p><?= nl2br(htmlspecialchars($sub['description'])) ?></p>
            <?php if ($sub['link']): ?>
                <a href="<?= htmlspecialchars($sub['link']) ?>" target="_blank" class="btn btn-outline-info btn-sm">🔗 Voir le lien</a>
            <?php endif; ?>
            <p class="text-muted small mt-2">Publié le <?= htmlspecialchars($sub['created_at']) ?></p>

            <!-- Vote -->
            <div class="d-flex align-items-center gap-3 mt-3">
                <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] != $sub['user_id']): ?>
                    <form method="POST" action="<?= $baseUrl ?>/app/controllers/VoteController.php">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">
                        <input type="hidden" name="challenge_id" value="<?= $sub['challenge_id'] ?>">
                        <button type="submit" class="btn <?= $hasVoted ? 'btn-danger' : 'btn-outline-danger' ?>">
                            <?= $hasVoted ? '💔 Retirer vote' : '❤️ Voter' ?>
                        </button>
                    </form>
                <?php endif; ?>
                <span class="fs-5 fw-bold">❤️ <?= $voteCount ?> vote(s)</span>
            </div>

            <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $sub['user_id']): ?>
                <div class="d-flex gap-2 mt-3">
                    <a href="edit_submission.php?id=<?= $sub['id'] ?>" class="btn btn-outline-secondary btn-sm">✏️ Modifier</a>
                    <form method="POST" action="<?= $baseUrl ?>/app/controllers/SubmissionController.php"
                          onsubmit="return confirm('Supprimer ?')">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $sub['id'] ?>">
                        <input type="hidden" name="challenge_id" value="<?= $sub['challenge_id'] ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm">🗑️ Supprimer</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Commentaires -->
    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="mb-0">💬 Commentaires (<?= count($comments) ?>)</h5>
        </div>
        <div class="card-body">

            <?php if (!empty($_SESSION['user_id'])): ?>
                <form method="POST" action="<?= $baseUrl ?>/app/controllers/CommentController.php" class="mb-4">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">
                    <input type="hidden" name="challenge_id" value="<?= $sub['challenge_id'] ?>">
                    <div class="mb-2">
                        <textarea name="content" class="form-control" rows="3"
                            placeholder="Votre commentaire..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">💬 Commenter</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">
                    <a href="login.php">Connectez-vous</a> pour commenter.
                </div>
            <?php endif; ?>

            <?php if (empty($comments)): ?>
                <p class="text-muted">Aucun commentaire pour l'instant.</p>
            <?php else: ?>
                <?php foreach ($comments as $cm): ?>
                    <div class="card mb-2 comment-card">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($cm['author_name']) ?></strong>
                                    <small class="text-muted ms-2"><?= htmlspecialchars($cm['created_at']) ?></small>
                                </div>
                                <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $cm['user_id']): ?>
                                    <form method="POST" action="<?= $baseUrl ?>/app/controllers/CommentController.php"
                                          onsubmit="return confirm('Supprimer ?')">
                                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $cm['id'] ?>">
                                        <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">
                                        <input type="hidden" name="challenge_id" value="<?= $sub['challenge_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">🗑️</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($cm['content'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>