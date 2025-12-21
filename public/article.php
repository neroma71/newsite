<?php
require_once __DIR__ . '/../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../utils/db_connect.php';
require_once __DIR__ . '/../utils/constants.php';

use App\Repository\ImageRepository;
use App\Repository\CategoryRepository;
use App\Repository\ArticleRepository;
use App\Controller\ArticleController;

$categoryRepository = new CategoryRepository($bdd);
$imageRepository = new ImageRepository($bdd);
$articleRepository = new ArticleRepository($bdd, $categoryRepository, $imageRepository);
$articleController = new ArticleController($articleRepository, $imageRepository, $categoryRepository); 

$id = isset($_GET['id']) ? $_GET['id'] : null;
// Redirection vers l'accueil si erreur
if (!is_numeric($id) || (int)$id <= 0) {
    header('Location: ' . BASE_URL);
    exit;
}
$id = (int)$id;

$categoryId = isset($_GET['category']) ? $_GET['category'] : null;
if ($categoryId !== null && (!is_numeric($categoryId) || (int)$categoryId <= 0)) {
    header('Location: ' . BASE_URL);
    exit;
}
if ($categoryId !== null) {
    $categoryId = (int)$categoryId;
}

$article = $articleRepository->findById($id);

if (!$article) {
    header('Location: ' . BASE_URL);
    exit;
}

// Si categoryId n'est pas passé ou n'existe pas en base, on le récupère depuis l'article
if (!$categoryId || !$categoryRepository->findById($categoryId)) {
    $categoryId = $article->getCategoryId();
}

$categorie = $categoryRepository->findById($categoryId); 
if (!$categorie) {
    header('Location: ' . BASE_URL);
    exit;
}
// Détermine l'id du précédent et du suivant
$prevNext = $articleController->getPrevNextArticleIds($id, $categoryId);
$prevId = $prevNext['prev'];
$nextId = $prevNext['next'];


$splitContent = ArticleController::extractYoutubeOembed($article->getContent());
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
     <link href="<?= BASE_URL ?>/public/css/article.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($article->getTitle()); ?></h1>
        <nav>
            <ul>
                <li>
                    <a class="nav-link active" href="<?= BASE_URL ?>">Accueil</a>
                </li>
                <li>
                    <a class="nav-link" href="<?= BASE_URL ?>#categories">Catégories</a>
                    <ul class="dropdown">
                        <?php foreach ($categoryRepository->findAll() as $category): ?>
                            <li><a class="nav-link" href="<?= BASE_URL ?>/categories.php?id=<?= $category->getId(); ?>"><?= htmlspecialchars($category->getTitle()); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>
    <div class="nav-btn">
        <div class="link-btn">
            <?php if ($prevId): ?>
               <div class="arrow-left"></div><a href="<?= BASE_URL ?>/article.php?id=<?= $prevId ?>&category=<?= $categoryId ?>">Précédent</a>
            <?php endif; ?>
            <?php if ($nextId): ?>
                <a href="<?= BASE_URL ?>/article.php?id=<?= $nextId ?>&category=<?= $categoryId ?>">Suivant</a><div class="arrow-right"></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <section class="col-12 col-sm-6  text">
                <?= $splitContent['text']; ?>
            </section>
            <section class="col-12 col-sm-6">
                <?php foreach ($splitContent['videos'] as $video): ?>
                    <div class="video-wrapper">
                        <?= $video ?>
                    </div>
                <?php endforeach; ?>
                <?php foreach ($article->getImages() as $image): ?>
                    <img src="<?= BASE_URL ?>/public/<?= htmlspecialchars($image->getPath()) ?>" 
                         alt="<?= htmlspecialchars($image->getImageTitle()) ?>" 
                         loading="lazy">
                <?php endforeach; ?>
            </section>
        </div>
    </div>
        <footer>
            <p>© 2024 Mon Site d'Articles</p>
        </footer>
        <script src="<?= BASE_URL ?>/public/js/article.js"></script>
</body>
</html>