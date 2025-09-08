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
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="css/manager.css" />
</head>
    <body>
    <h2>Créer une catégorie</h2>
    <div class="container">
    <form method="post" enctype="multipart/form-data" action="">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="mb-3">
            <label for="title">Titre :</label>
            <input type="text" name="title" id="title"  class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description">Description :</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="image">Image :</label>
            <input type="file" name="image" id="image" accept="image/*" class="form-control"><br />
            <small>Formats acceptés : jpg, jpeg, png, gif. Taille max : 2 Mo.</small>
        </div>
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
    </div>
    </body>
</html>