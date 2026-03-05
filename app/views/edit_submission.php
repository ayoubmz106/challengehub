<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/Submission.php";
require_once __DIR__ . "/../../config/csrf.php";

$id              = (int)($_GET['id'] ?? 0);
$submissionModel = new Submission();
$sub             = $submissionModel->getById($id);

if (!$sub || $sub['user_id'] != $_SESSION['user_id']) {
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
    <title>Modifier ma participation – ChallengeHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body class="bg-light">
<?php include 'partials/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">✏️ Modifier ma participation</h4>
                </div>
                <div class="card-body p-4">

                    <?php if (!empty($_GET['error'])): ?>
                        <div class="alert alert-danger">La description est obligatoire.</div>
                    <?php endif; ?>

                    <form method="POST" action="<?= $baseUrl ?>/app/controllers/SubmissionController.php" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $sub['id'] ?>">
                        <input type="hidden" name="challenge_id" value="<?= $sub['challenge_id'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($sub['description']) ?></textarea>
                        </div>

                        <?php if ($sub['image']): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Image actuelle</label><br>
                                <img src="../../public/images/submissions/<?= htmlspecialchars($sub['image']) ?>"
                                     height="100" class="rounded border">
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nouvelle image <span class="text-muted">(optionnel)</span></label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Lien externe <span class="text-muted">(optionnel)</span></label>
                            <input type="url" name="link" class="form-control"
                                   value="<?= htmlspecialchars($sub['link'] ?? '') ?>" placeholder="https://...">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-4">💾 Enregistrer</button>
                            <a href="challenge_detaille.php?id=<?= $sub['challenge_id'] ?>" class="btn btn-outline-secondary">Annuler</a>
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