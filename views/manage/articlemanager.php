<?php
require_once __DIR__ . '/../../utils/session_init.php';
require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';

use App\Repository\ImageRepository;
use App\Repository\CategoryRepository;  
use App\Repository\ArticleRepository;
use App\Controller\ArticleController;  


$categoryRepository = new CategoryRepository($bdd);
$imageRepository = new ImageRepository($bdd);
$articleRepository = new ArticleRepository($bdd, $categoryRepository, $imageRepository);
$controller = new ArticleController($articleRepository, $imageRepository);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $controller->delete((int)$_POST['delete_id'], $_POST['csrf_token']);
    } catch (\Exception $e) {
        echo "Erreur : " . htmlspecialchars($e->getMessage());
    }
}

$articles = $articleRepository->findAllWithCategory();
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
        <p>manager article</p>
     </header>
    <div class="container">
        <p>
         <a href="createArticle.php" class="btn btn-primary">Créer un article</a>
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
                <td><a href="editArticle.php?id=<?= $article->getId() ?>" class="btn btn-primary">Modifier</a> </td>
                <td>
                    <form method="post" action="" style="display:inline;">
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
