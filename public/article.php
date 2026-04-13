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
                        <?php foreach ($categories as $category): ?>
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