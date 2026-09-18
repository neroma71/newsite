<?php
/** @var \App\Entity\Articles[] $articles */

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Articles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="css/manager.css" />
</head>
<body>
     <header>
        <p>Gérer les articles</p>
     </header>
    <div class="container">
        <p>
         <a href="<?= BASE_URL ?>/manage/articles/create" class="btn btn-primary">Créer un article</a>
        </p>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Modifier</th>
                <th>Supprimer</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($articles as $article): ?>
            <tr>
                <td><?= htmlspecialchars($article->getId()) ?></td>
                <td><?= htmlspecialchars($article->getTitle()) ?></td>
                 <td><?= htmlspecialchars($article->getCategoryTitle()) ?></td>
                <td><a href="<?= BASE_URL ?>/manage/articles/edit?id=<?= $article->getId() ?>" class="btn btn-primary"> Modifier</a> </td>
                <td>
                    <form method="post" action="<?= BASE_URL ?>/manage/articles/delete" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= $article->getId() ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
        </div>
</body>
</html>
