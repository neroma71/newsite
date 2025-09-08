<?php
require_once __DIR__ . '/../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../utils/db_connect.php';

use App\Repository\ImageRepository;
use App\Repository\CategoryRepository;
use App\Repository\ArticleRepository;
use App\Controller\ArticleController;

$categoryRepository = new CategoryRepository($bdd);
$imageRepository = new ImageRepository($bdd);
$articleRepository = new ArticleRepository($bdd, $categoryRepository, $imageRepository);
$articleController = new ArticleController($articleRepository, $imageRepository);

$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$query = isset($_GET['query']) ? trim($_GET['query']) : null;

if(empty($query)) {
    header("Location: categories.php?id=$categoryId");
    exit;
}
if(!$categoryId){
    echo "Catégorie non spécifiée.";
    exit;
}
try
{
$articles = $articleRepository->findArticlesByCategoryAndQuery($categoryId, $query);
$categories = $categoryRepository->findAll();
$category = $categoryRepository->findById($categoryId);
$categoryTitle = htmlspecialchars($category ? $category->getTitle() : 'Catégorie inconnue');
}catch (Exception $e) {
    echo "Erreur lors de la récupération des articles : " . $e->getMessage();
    exit;
}

$homeRepository = new \App\Repository\HomeRepository($bdd);
$homes = $homeRepository->findAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/categorie.css">
</head>
<body>
    <div class="search-form">
            <form action="result.php" method="GET">
                <input type="hidden" name="category" value="<?= $categoryId ?>">
                <input type="text" name="query" id="query" placeholder="Rechercher">
                <button type="submit" class="submit"></button>
            </form>
        </div>
    <header>
        <div id="logo">
            <?php if (!empty($homes) && $homes[0]->getImage1()): ?>
                <img src="uploads/<?= htmlspecialchars($homes[0]->getImage1()) ?>" alt="Logo">
            <?php endif; ?>     
        </div>
        <nav>
            <ul>
                <li>
                    <a class="nav-link active" href="./index.php">Accueil</a>
                </li>
                <li>
                    <a class="nav-link" href="./index.php#categories">Catégories</a>
                    <ul class="dropdown">
                        <?php foreach ($categoryRepository->findAll() as $category): ?>
                            <li><a class="nav-link" href="categories.php?id=<?= $category->getId(); ?>"><?= htmlspecialchars($category->getTitle()); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
        </nav>
      <h1><?= htmlspecialchars($category ? $category->getTitle() : 'Catégorie inconnue') ?></h1>
      <p><?= htmlspecialchars($category ? $category->getDescription() : '') ?></p>
    </header>
    <main>
        <div class="category" style="background:url(./uploads/<?= htmlspecialchars($category ? $category->getImage() : '') ?>) no-repeat; background-size:cover;"></div>
        <section class="articles">
            <?php foreach ($articles as $article): ?>
                <a href="article.php?id=<?= htmlspecialchars($article->getId());?>">
                <article class="article">
                    <div class="rollover"></div>
                        <h2><?= htmlspecialchars($article->getTitle()) ?></h2>
                        <?php 
                        $images = $article->getImages();
                        if (!empty($images)) {
                            $firstImage = $images[0];
                        ?>
                            <img src="../public/<?= htmlspecialchars($firstImage->getPath()) ?>" alt="<?= htmlspecialchars($firstImage->getImageTitle()) ?>">
                        <?php } ?>
                </article>
                </a>
            <?php endforeach; ?>

            <?php if (empty($articles)): ?>
                <p>Aucun article trouvé dans cette catégorie.</p>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>du blabla</p>
    </footer>

    <script src="js/cat.js"></script>
</body>
</html>