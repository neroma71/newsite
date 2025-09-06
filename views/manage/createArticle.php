<?php
require_once __DIR__ . '/../../utils/session_init.php';
require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';
use App\Repository\ArticleRepository;
use App\Controller\ArticleController;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;

$categoryRepository = new CategoryRepository($bdd);
$categories = $categoryRepository->findAll(); 
$imageRepository = new ImageRepository($bdd);

$articleRepository = new ArticleRepository($bdd, $categoryRepository, $imageRepository);
$controller = new ArticleController($articleRepository, $imageRepository);

// Traitement du formulaire si POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->create();
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="css/manager.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="articlemanager.php">Gérer les articles</a></li>
                <li><a href="category.php">Gérer les catégories</a></li>
            </ul>
        </nav>
        <p>créer un article</p>
    </header>
    <div class="container">
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="formdiv">
                <p><label for="title">Titre de l'article :</label></p>
                <input type="text" id="title" name="title" required>
            </div><br />
            <div class="formdiv">
                <p><label for="content">Contenu de l'article :</label></p>
                <textarea id="content" name="content" rows="10"></textarea>
            </div>                      

            <div class="formdiv">
                <p><label for="category_id">Catégorie de l'article :</label></p>
                <select id="category_id" class="form-select" name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->getId() ?>"><?= htmlspecialchars($category->getTitle()) ?></option>
                <?php endforeach; ?>
            </select>
            </div>
            <div class="formdiv">                                                        
                <p><label for="image">Ajouter des images :</label></p>
                <input type="file" id="image" name="images[]" accept="image/*" multiple class="btn btn-primary">    
            </div>
            <div class="formdiv">
                <button type="submit" class="btn btn-primary">Créer l'article</button>
            </div>
        </form>
    </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create( document.querySelector( '#content' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
    </body>
    </html>