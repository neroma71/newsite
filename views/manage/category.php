<?php
require_once __DIR__ . '/../../utils/session_init.php';
require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';
use App\Repository\CategoryRepository;
use App\Controller\CategoryController;

$categoryRepository = new CategoryRepository($bdd);
$controller = new CategoryController($categoryRepository);

// Suppression via contrôleur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $controller->delete((int)$_POST['delete_id'], $_POST['csrf_token'] ?? '');
}

$categories = $categoryRepository->findAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
        <link rel="stylesheet" href="css/manager.css">
</head>
<body>
    <div class="container">
        <h1>Gestion des catégories</h1>
        <p><a href="createCategory.php" class="btn btn-primary">Créer une catégorie</a></p>
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
                    <td><?= htmlspecialchars($category->getId()) ?></td>
                    <td><?= htmlspecialchars($category->getTitle()) ?></td>
                    <td><?= htmlspecialchars(strip_tags($category->getDescription())) ?></td>
                    <td>
                        <?php if ($category->getImage()): ?>
                            <img src="../../public/uploads/<?= htmlspecialchars($category->getImage()) ?>" style="max-width:40px;max-height:40px;" alt="image">
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-primary">
                            <a href="editCategory.php?id=<?= $category->getId() ?>">Éditer</a>
                        </button>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $category->getId() ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
</body>
</html>