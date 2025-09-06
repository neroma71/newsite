<?php
require_once __DIR__ . '/../../utils/session_init.php';

require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';
use App\Repository\ImageRepository;
use App\Repository\CategoryRepository;
use App\Repository\ArticleRepository;
use App\Controller\ArticleController;


$csrfToken = $_SESSION['csrf_token'];

// Récupération de l'ID de l'article à modifier
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$categoryRepository = new CategoryRepository($bdd);
$imageRepository = new ImageRepository($bdd);
$articleRepository = new ArticleRepository($bdd, $categoryRepository, $imageRepository);

// Traitement du formulaire si POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ArticleController($articleRepository, $imageRepository);
    $controller->update($id);
    exit;
}

// Récupération de l'article pour affichage
$article = $id ? $articleRepository->findById($id) : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'article</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="css/manager.css" />
</head>
<body>
    <header>
        <p>Modifier l'article</p>
    </header>
    <div class="container">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="formdiv">
                <p><label for="title">Titre :</label></p>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($article->getTitle()) ?>" required> 
            </div>
        <div class="formdiv">
                <p><label for="content">Contenu :</label></p>
                <textarea name="content" id="content" required><?= htmlspecialchars($article->getContent()) ?></textarea>
            </div>
        <div class="formdiv">
                <p><label for="category_id">Choisir la catégorie :</label></p>
                <select name="category_id" id="category_id" required>
                    <?php foreach ($categoryRepository->findAll() as $category): ?>
                        <option value="<?= $category->getId() ?>" <?= $article->getCategoryId() === $category->getId() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category->getTitle()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="formdiv image-section">
             <?php if ($article->getImages()): ?>
                <?php foreach ($article->getImages() as $image): ?>
                    <div class="formdiv image-item" data-image-id="<?= $image->getId() ?>">
                        <div class="forminput">
                            <img src="../../public<?= htmlspecialchars($image->getPath()) ?>" alt="<?= htmlspecialchars($image->getImageTitle()) ?>" id="img-preview-<?= $image->getId() ?>">
                        </div>
                            <p><label for="image_title_<?= $image->getId() ?>">Changer le nom de l'image :</label></p>
                        <div class="forminput">
                            <input type="text" name="image_titles[<?= $image->getId() ?>]" id="image_title_<?= $image->getId() ?>" value="<?= htmlspecialchars($image->getImageTitle()) ?>">
                        </div>
                        <p><label for="image_file_<?= $image->getId() ?>">Remplacer l'image :</label></p>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <div class="forminput">
                            <input type="file" name="image_files[<?= $image->getId() ?>]" id="image_file_<?= $image->getId() ?>" accept="image/*">
                        </div>
                        <div class="forminput">
                            <label>
                                <input type="checkbox" name="delete_images[]" value="<?= $image->getId() ?>">
                                Supprimer cette image
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune image pour cet article.</p>
            <?php endif; ?>
            </div>
            <div class="formdiv">
            <p>Ajouter de nouvelles images</p>
            <input type="file" name="images[]" id="images" accept="image/*" multiple>
            </div>
            <div class="formdiv">
            <button type="submit">Mettre à jour</button>
            </div>
        </form>
        <footer></footer>
    </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script src="js/updateImage.js"></script>
    <script>
        ClassicEditor
            .create( document.querySelector( '#content' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
</body>
</html>