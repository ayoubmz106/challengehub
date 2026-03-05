<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/Challenge.php";
require_once __DIR__ . "/../models/Submission.php";
require_once __DIR__ . "/../../config/csrf.php";

$challenge_id   = (int)($_GET['challenge_id'] ?? 0);
$challengeModel = new Challenge();
$challenge      = $challengeModel->getById($challenge_id);

if (!$challenge) {
    header("Location: challenge.php");
    exit();
}

$base   = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$folder = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl = $base . $folder;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Participer – ChallengeHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body class="bg-light">
<?php include 'partials/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">🚀 Participer au défi</h4>
                </div>
                <div class="card-body p-4">

                    <div class="alert alert-info mb-4">
                        🎯 Défi : <strong><?= htmlspecialchars($challenge['title']) ?></strong>
                        · 📅 Deadline : <strong><?= htmlspecialchars($challenge['deadline']) ?></strong>
                    </div>

                    <?php if (!empty($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_GET['error'] === 'already' ? 'Vous avez déjà participé à ce défi.' : 'La description est obligatoire.' ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= $baseUrl ?>/app/controllers/SubmissionController.php" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="action" value="create">
                        <input type="hidden" name="challenge_id" value="<?= $challenge_id ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5"
                                      placeholder="Décrivez votre participation..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Image <span class="text-muted">(optionnel)</span></label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Lien externe <span class="text-muted">(optionnel)</span></label>
                            <input type="url" name="link" class="form-control" placeholder="https://...">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4">✅ Soumettre</button>
                            <a href="challenge_detaille.php?id=<?= $challenge_id ?>" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>