<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/categorie.css">
</head>
<body>
     <div class="search-form">
            <form method="GET">
    <input type="hidden" name="id" value="<?= $categoryId ?>">
    <input type="text" name="search"
           value="<?= htmlspecialchars($search ?? '') ?>"
           placeholder="Rechercher">
    <button type="submit"></button>
</form>
        </div>
    <header>
        <a href="<?= BASE_URL ?>">
        <div id="logo">
             <!-- image du logo -->
        </div>
        </a>
        <nav>
            <ul>
                <li>
                    <a class="nav-link active" href="<?= BASE_URL ?>/index.php">Accueil</a>
                </li>
                <li>
                    <a class="nav-link active" href="<?= BASE_URL ?>/actu.php">Actus</a>
                </li>
                <li>
                    <a class="nav-link" href="<?= BASE_URL ?>/index.php#categories">Catégories</a>
                    <ul class="dropdown">
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a class="nav-link" 
                                   href="<?= BASE_URL ?>/categories.php?id=<?= $category->getId(); ?>">
                                    <?= htmlspecialchars($category->getTitle()); ?>
                                </a>
                            </li>
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
        <div class="category" style="background:url(<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($categorie->getImage()) ?>) no-repeat; background-size:cover;"></div>
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
                <a href="<?= BASE_URL ?>/article.php?id=<?= htmlspecialchars($article->getId());?>">
                <article class="article">
                    <div class="rollover"></div>
                        <h2><?= htmlspecialchars($article->getTitle()) ?></h2>
                        <?php 
                        $images = $article->getImages();
                        if (!empty($images)) {
                            $firstImage = $images[0];
                        ?>
                            <img src="<?= BASE_URL ?><?= htmlspecialchars($firstImage->getPath()) ?>" alt="<?= htmlspecialchars($firstImage->getImageTitle()) ?>">
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
    <script src="<?= BASE_URL ?>/public/js/cat.js"></script>
</body>
</html>
