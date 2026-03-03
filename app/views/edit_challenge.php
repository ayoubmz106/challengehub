<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/Challenge.php";

$id = (int)($_GET['id'] ?? 0);
$challengeModel = new Challenge();
$c = $challengeModel->getById($id);

if (!$c || $c['user_id'] != $_SESSION['user_id']) {
    header("Location: challenge.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le défi</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="form-container">
    <h2>Modifier le défi</h2>

    <form method="POST" action="/challengeshub/app/controllers/challengecontroller.php" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $c['id'] ?>">
        <label>Titre *</label>
        <input type="text" name="title" value="<?= htmlspecialchars($c['title']) ?>" required>
        <label>Description *</label>
        <textarea name="description" rows="5" required><?= htmlspecialchars($c['description']) ?></textarea>
        <label>Catégorie *</label>
        <input type="text" name="category" value="<?= htmlspecialchars($c['category']) ?>" required>
        <label>Date limite *</label>
        <input type="date" name="deadline" value="<?= htmlspecialchars($c['deadline']) ?>" required>
        <?php if ($c['image']): ?>
            <p>Image actuelle :<br>
            <img src="../../public/images/challenges/<?= htmlspecialchars($c['image']) ?>" height="80"></p>
        <?php endif; ?>
        <label>Nouvelle image (optionnel)</label>
        <input type="file" name="image" accept="image/*">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="challenges.php" class="btn">Annuler</a>
    </form>
</div>
</body>
</html>