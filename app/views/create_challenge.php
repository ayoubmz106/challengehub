<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un défi</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="form-container">
    <h2>Créer un défi</h2>

    <?php if (!empty($_GET['error'])): ?>
        <p class="alert error">Remplissez tous les champs obligatoires.</p>
    <?php endif; ?>

<form method="POST" action="/challengeshub/app/controllers/challengecontroller.php" enctype="multipart/form-data">        <input type="hidden" name="action" value="create">
        <label>Titre *</label>
        <input type="text" name="title" required>
        <label>Description *</label>
        <textarea name="description" rows="5" required></textarea>
        <label>Catégorie *</label>
        <input type="text" name="category" placeholder="Art, Photo, Code, Musique..." required>
        <label>Date limite *</label>
        <input type="date" name="deadline" required min="<?= date('Y-m-d') ?>">
        <label>Image (optionnel)</label>
        <input type="file" name="image" accept="image/*">
        <button type="submit" class="btn btn-primary">Publier</button>
        <a href="challenge.php" class="btn">Annuler</a>
    </form>
</div>
</body>
</html>