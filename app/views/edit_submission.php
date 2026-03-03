<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/Submission.php";

$id = (int)($_GET['id'] ?? 0);
$submissionModel = new Submission();
$sub = $submissionModel->getById($id);

if (!$sub || $sub['user_id'] != $_SESSION['user_id']) {
    header("Location: challenge.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier ma participation</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="form-container">
    <h2>Modifier ma participation</h2>

    <form method="POST" action="/<?= trim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>/app/controllers/submissioncontroller.php" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $sub['id'] ?>">
        <input type="hidden" name="challenge_id" value="<?= $sub['challenge_id'] ?>">

        <label>Description *</label>
        <textarea name="description" rows="5" required><?= htmlspecialchars($sub['description']) ?></textarea>

        <?php if ($sub['image']): ?>
            <p>Image actuelle :<br>
            <img src="../../public/images/submissions/<?= htmlspecialchars($sub['image']) ?>" height="80"></p>
        <?php endif; ?>
        <label>Nouvelle image (optionnel)</label>
        <input type="file" name="image" accept="image/*">

        <label>Lien externe</label>
        <input type="url" name="link" value="<?= htmlspecialchars($sub['link'] ?? '') ?>">

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="challenge_detaille.php?id=<?= $sub['challenge_id'] ?>" class="btn">Annuler</a>
    </form>
</div>
</body>
</html>