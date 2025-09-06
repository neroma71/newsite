<?php
require_once __DIR__ . '/../../utils/session_init.php';
require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';
use App\Repository\CategoryRepository;
use App\Controller\CategoryController;
$categoryRepository = new CategoryRepository($bdd);
$controller = new CategoryController($categoryRepository);
// Traitement du formulaire si POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->create();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
    <body>
    <h2>Créer une catégorie</h2>
    <form method="post" enctype="multipart/form-data" action="">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div>
            <label for="title">Titre :</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div>
            <label for="description">Description :</label>
            <textarea name="description" id="description" required></textarea>
        </div>
        <div>
            <label for="image">Image :</label>
            <input type="file" name="image" id="image" accept="image/*"><br />
            <small>Formats acceptés : jpg, jpeg, png, gif. Taille max : 2 Mo.</small>
        </div>
        <button type="submit">Créer</button>
    </form>
    </body>
</html>