<?php
require_once __DIR__ . '/../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../utils/db_connect.php';

use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\HomeRepository;
use App\Repository\ArticleRepository; 
use App\Controller\ArticleController;

$categoryRepository = new CategoryRepository($bdd);
$imageRepository = new ImageRepository($bdd);
$homeRepository = new HomeRepository($bdd);
$articleRepository = new ArticleRepository($bdd, $categoryRepository, $imageRepository); // Utilisation de l'objet

$categoryId = isset($_GET['id']) ? (int) $_GET['id'] : null;
if (!$categoryId) {
     header('Location: /404.php');
    exit;
}

$categorie = $categoryRepository->findById($categoryId);

// Instancie le contrôleur ArticleController
$articleController = new ArticleController($articleRepository, $imageRepository);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

$limit = 5; // nombre d'articles par page

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($searchQuery) {
    $articles = $articleRepository->findArticlesByCategoryAndQuery($categoryId, $searchQuery);
} else {
    // pagination du contrôleur
    $paginationData = $articleController->getPaginatedData($categoryId, $page, $limit);

    $articles = $paginationData['articles'];
    $currentPage = $paginationData['currentPage'];
    $totalPages = $paginationData['totalPages'];
    $totalArticles = $paginationData['totalArticles'];
}

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
        <a href="index.php">
        <div id="logo">
            <?php if ($homes[0]->getImage1()): ?>
                <img src="uploads/<?= htmlspecialchars($homes[0]->getImage1()) ?>" alt="Logo">
            <?php endif; ?>     
        </div>
        </a>
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
      <h1><?= htmlspecialchars($categorie->getTitle()) ?></h1>
      <p><?= htmlspecialchars($categorie->getDescription()) ?></p>
    </header>
    <!-- ... ton header ... -->
    <main>
        <div class="category" style="background:url(./uploads/<?= htmlspecialchars($categorie->getImage()) ?>) no-repeat; background-size:cover;"></div>
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <ul>
                    <?php if ($currentPage > 1): ?>
                        <li class="nextprev">
                            <a href="?id=<?= $categoryId ?>&page=<?= $currentPage - 1 ?>">
                                Précédent
                            </a>
                             <div class="arrow-left"></div>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li>
                            <a href="?id=<?= $categoryId ?>&page=<?= $i ?>"
                               <?= $i === $currentPage ? 'style="font-weight: bold;"' : '' ?>>
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <li class="nextprev">
                            <a href="?id=<?= $categoryId ?>&page=<?= $currentPage + 1 ?>">
                                Suivant 
                            </a>
                            <div class="arrow-right"></div> 
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
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
</body>
</html>
</html>
