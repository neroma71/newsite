<?php
/** @var \App\Entity\Actu[] $actus */
/** @var \App\Entity\Home|null $home */
/** @var \App\Entity\Category[] $categories */
/** @var int $totalPages */
/** @var int $currentPage */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualités</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/actu.css">
</head>
<body>
<main>
    <header>
            <div id="logo">
            <?php if (!empty($homes) && $homes[0]->getImage1()): ?>
                <img src="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($homes[0]->getImage1()) ?>">
            <?php endif; ?>   
        </div>
           <h1>Actualités</h1>
         <nav>
            <ul>
                <li>
                    <a class="nav-link active" href="<?= BASE_URL ?>/index.php">Accueil</a>
                </li>
                <li>
                    <a class="nav-link" href="<?= BASE_URL ?>/index.php#categories">Galeries</a>
                    <ul class="dropdown">
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="<?= BASE_URL ?>/categories.php?id=<?= $category->getId(); ?>">
                                <?= htmlspecialchars($category->getTitle()); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
        </nav>
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <ul>
                    <?php if ($currentPage > 1): ?>
                        <li class="nextprev">
                            <a href="?page=<?= $currentPage - 1 ?>">
                                Précédent
                            </a>
                            <div class="arrow-left"></div>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li>
                            <a href="?page=<?= $i ?>"
                               <?= $i === $currentPage ? 'style="font-weight: bold;"' : '' ?>>
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="nextprev">
                            <a href="?page=<?= $currentPage + 1 ?>">
                                Suivant
                            </a>
                            <div class="arrow-right"></div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </header>       
    <section id="actus">
        <?php foreach ($actus as $actu): ?>
            <article class="actu-item">
                <h2><?= htmlspecialchars($actu->getTitle()) ?></h2>
                <p class="date"> posté le <?= $actu->getCreatedAt()->format('d/m/Y à H:i') ?> h</p>
                  <?php if ($actu->getImage()): ?>
                    <!-- Affichage de l'image tronquée uniquement décoratif -->
                    <div class="actu-header">
                        <img src="/newsite/public/uploads/<?= htmlspecialchars($actu->getImage()) ?>" alt="Image de l'actualité">
                    </div>
                <?php endif; ?>
                  <div class="actu-content">
                    <?= $actu->getContent(); ?>
                </div>
                <?php if ($actu->getImage()): ?>
                    <div class="actu-image">
                        <img src="/newsite/public/uploads/<?= htmlspecialchars($actu->getImage()) ?>" alt="Image de l'actualité">
                    </div>
                <?php endif; ?>
            </article>                              
        <?php endforeach; ?>
    </section>
</main>
 <script src="<?= BASE_URL ?>/public/js/actu.js"></script>
</body>
</html>