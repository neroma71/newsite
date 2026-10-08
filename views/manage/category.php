<?php
/** @var \App\Entity\Category[] $categories */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des catégories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/manager.css">
</head>
<body>
<header>
    <p>Gestion des catégories</p>
    <nav>
        <ul>
            <li>
                <a href="<?= BASE_URL ?>/manage/dashboard">dashboard</a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/manage/home">Gérer l'accueil</a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/manage/articles">Gérer les articles</a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/manage/actus">Gérer les actus</a>
            </li>
        </ul>
    </nav>
</header>
<div class="container">
    <p>
        <a href="<?= BASE_URL ?>/manage/categories/create"
           class="btn btn-primary">
            Créer une catégorie
        </a>
    </p>
    <table class="table mt-5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($category->getId()) ?>
                </td>
                <td>
                    <?= htmlspecialchars($category->getTitle()) ?>
                </td>
                <td>
                    <?= htmlspecialchars(strip_tags($category->getDescription())) ?>
                </td>
                <td>
                    <?php if ($category->getImage()): ?>
                        <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($category->getImage()) ?>"
                             style="max-width:40px;max-height:40px;"
                             alt="image">
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>/manage/categories/edit?id=<?= $category->getId() ?>"
                       class="btn btn-primary">
                        Éditer
                    </a>
                    <form method="POST"
                          action="<?= BASE_URL ?>/manage/categories/delete"
                          style="display:inline;">
                        <input type="hidden"
                               name="delete_id"
                               value="<?= $category->getId() ?>">
                        <input type="hidden"
                               name="csrf_token"
                               value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <button type="submit"
                                class="btn btn-danger">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>