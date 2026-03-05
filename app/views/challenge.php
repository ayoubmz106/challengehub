<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/Challenge.php";
require_once __DIR__ . "/../../config/csrf.php";

$base   = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$folder = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl = $base . $folder;

$challengeModel = new Challenge();
$keyword  = htmlspecialchars($_GET['keyword'] ?? '');
$category = htmlspecialchars($_GET['category'] ?? '');
$sort     = $_GET['sort'] ?? 'date';

$challenges = $challengeModel->getAll($keyword, $category, $sort);
$categories = $challengeModel->getCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Défis – ChallengeHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body class="bg-light">
<?php include 'partials/navbar.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">🎯 Tous les défis</h1>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="create_challenge.php" class="btn btn-primary">+ Créer un défi</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_GET['success'] === 'created' ? 'Défi créé !' : ($_GET['success'] === 'updated' ? 'Défi modifié !' : 'Défi supprimé.') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filtres -->
    <form method="GET" action="" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="keyword" class="form-control" placeholder="🔍 Rechercher..." value="<?= $keyword ?>">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="sort" class="form-select">
                <option value="date"       <?= $sort === 'date'       ? 'selected' : '' ?>>Plus récents</option>
                <option value="popularity" <?= $sort === 'popularity' ? 'selected' : '' ?>>Plus populaires</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary w-100">Filtrer</button>
        </div>
    </form>

    <!-- Liste défis -->
    <div class="row g-4">
        <?php if (empty($challenges)): ?>
            <div class="col-12">
                <div class="alert alert-info">Aucun défi trouvé.</div>
            </div>
        <?php else: ?>
            <?php foreach ($challenges as $c): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm challenge-card">
                        <?php if ($c['image']): ?>
                            <img src="../../public/images/challenges/<?= htmlspecialchars($c['image']) ?>"
                                 class="card-img-top challenge-img" alt="image">
                        <?php endif; ?>
                        <div class="card-body">
                            <span class="badge bg-info text-dark mb-2"><?= htmlspecialchars($c['category']) ?></span>
                            <h5 class="card-title">
                                <a href="challenge_detaille.php?id=<?= $c['id'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($c['title']) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted small"><?= htmlspecialchars(substr($c['description'], 0, 100)) ?>...</p>
                        </div>
                        <div class="card-footer bg-white">
                            <small class="text-muted">
                                Par <strong><?= htmlspecialchars($c['author_name']) ?></strong>
                                · 📅 <?= htmlspecialchars($c['deadline']) ?>
                                · 👥 <?= $c['submission_count'] ?> participation(s)
                            </small>
                            <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']): ?>
                                <div class="mt-2 d-flex gap-2">
                                    <a href="edit_challenge.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-secondary">✏️</a>
                                    <form method="POST" action="<?= $baseUrl ?>/app/controllers/ChallengeController.php"
                                          style="display:inline" onsubmit="return confirm('Supprimer ?')">
                                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">🗑️</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>