<?php
require_once __DIR__ . '/../../utils/session_init.php';
require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';
use App\Repository\CategoryRepository;
use App\Controller\CategoryController;
$categoryRepository = new CategoryRepository($bdd);
$controller = new CategoryController($categoryRepository);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Traitement du formulaire si POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->update($id);
    exit;
}
// On récupère toujours l'objet Category pour affichage
$category = $id ? $categoryRepository->findById($id) : null;    
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
    <h2>Créer une catégorie</h2>
    <div class="container">
    <form method="post" enctype="multipart/form-data" action="">
        <div class="formdiv">
            <label for="title">Titre :</label>
            <input type="text" name="title" id="title" value='<?= htmlspecialchars($category->getTitle()) ?>'> 
        </div>
        <div class="formdiv">
            <p><label for="description">Description :</label></p>
            <textarea name="description" id="description" required><?= htmlspecialchars($category->getDescription()) ?></textarea>
        </div>
        <div class="formdiv">
            <p><label for="image">Image :</label></p>
            <input type="file" name="image" id="image" accept="image/*" class="btn btn-primary"><br />
        </div>
        <div class="formdiv">
             <?php if ($category->getImage()): ?>
            <img src="../../../public/uploads/<?= htmlspecialchars($category->getImage()) ?>" style="max-width:100px;max-height:100px;" alt="Image 3 actuelle"><br>
            <?php endif; ?>
        </div>
        <div class="formdiv">
            <button type="submit" class="btn btn-primary">Créer</button>
        </div>
    </form>
    </div>
    </body>
</html>