<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/Challenge.php";

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
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container">
    <h1>Tous les défis</h1>

    <?php if (!empty($_GET['success'])): ?>
        <p class="alert success">
            <?= $_GET['success'] === 'created' ? 'Défi créé !' : ($_GET['success'] === 'updated' ? 'Défi modifié !' : 'Défi supprimé.') ?>
        </p>
    <?php endif; ?>

    <!-- Filtres -->
    <form method="GET" action="" class="filters">
        <input type="text" name="keyword" placeholder="Rechercher..." value="<?= $keyword ?>">
        <select name="category">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="sort">
            <option value="date"       <?= $sort === 'date'       ? 'selected' : '' ?>>Plus récents</option>
            <option value="popularity" <?= $sort === 'popularity' ? 'selected' : '' ?>>Plus populaires</option>
        </select>
        <button type="submit">Filtrer</button>
    </form>

    <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="create_challenge.php" class="btn btn-primary">+ Créer un défi</a>
    <?php endif; ?>

    <!-- Liste -->
    <div class="challenges-grid">
        <?php if (empty($challenges)): ?>
            <p>Aucun défi trouvé.</p>
        <?php else: ?>
            <?php foreach ($challenges as $c): ?>
                <div class="challenge-card">
                    <?php if ($c['image']): ?>
                        <img src="../../public/images/challenges/<?= htmlspecialchars($c['image']) ?>" alt="image">
                    <?php endif; ?>
                    <span class="badge"><?= htmlspecialchars($c['category']) ?></span>
                    <h2>
                        <a href="challenge_detaille.php?id=<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['title']) ?>
                        </a>
                    </h2>
                    <p><?= htmlspecialchars(substr($c['description'], 0, 100)) ?>...</p>
                    <p class="meta">
                        Par <strong><?= htmlspecialchars($c['author_name']) ?></strong>
                        · Deadline : <?= htmlspecialchars($c['deadline']) ?>
                        · <?= $c['submission_count'] ?> participation(s)
                    </p>

                    <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']): ?>
                        <div class="card-actions">
                            <a href="edit_challenge.php?id=<?= $c['id'] ?>" class="btn btn-sm">✏️ Modifier</a>
                            <form method="POST" action="/challengeshub/app/controllers/challengecontroller.php"
                                  style="display:inline"
                                  onsubmit="return confirm('Supprimer ce défi ?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">🗑️ Supprimer</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>