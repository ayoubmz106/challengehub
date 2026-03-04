<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/challenge.php";

$challenge_id = (int)($_GET['challenge_id'] ?? 0);
$challengeModel = new Challenge();
$challenge = $challengeModel->getById($challenge_id);

if (!$challenge) {
    header("Location: challenge.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Participer – <?= htmlspecialchars($challenge['title']) ?></title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="form-container">
    <h2>Participer au défi</h2>
    <p class="meta">Défi : <strong><?= htmlspecialchars($challenge['title']) ?></strong></p>

    <?php if (!empty($_GET['error'])): ?>
        <p class="alert error">
            <?= $_GET['error'] === 'already' ? 'Vous avez déjà participé à ce défi.' : 'Remplissez la description.' ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="/<?= trim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>/app/controllers/submissioncontroller.php" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <input type="hidden" name="challenge_id" value="<?= $challenge_id ?>">

        <label>Description *</label>
        <textarea name="description" rows="5" required placeholder="Décrivez votre participation..."></textarea>

        <label>Image (optionnel)</label>
        <input type="file" name="image" accept="image/*">

        <label>Lien externe (optionnel)</label>
        <input type="url" name="link" placeholder="https://...">

        <button type="submit" class="btn btn-primary">Soumettre</button>
        <a href="challenge_detaille.php?id=<?= $challenge_id ?>" class="btn">Annuler</a>
    </form>
</div>
</body>
</html>