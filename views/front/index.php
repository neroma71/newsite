<?php
/** @var \App\Entity\Home[] $homes */
/** @var \App\Entity\Category[] $categories */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
<main>
<?php foreach ($homes as $home): ?>
    <header>
        <?php include __DIR__ . './../../partials/menu.php'; ?>
        <?php include __DIR__ . './../../partials/responsivemenu.php'; ?>
    </header>

    <div id="logo">
        <?php if ($home->getImage1()): ?>
            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($home->getImage1()) ?>" alt="Section">
        <?php endif; ?>
    </div>

    <section id="accueil">
        <div id="title">
               <svg width="95%" height="90%" xmlns="http://w3.org">
                    <rect x="4" y="4" width="calc(100% - 8px)" height="calc(100% - 8px)" fill="transparent" stroke="white" stroke-width="1" pathLength="100" class="draw-rect" />
               </svg>
                 <h1><?= htmlspecialchars($home->getTitle()) ?></h1>
                <h2 class="subtitle"><?= htmlspecialchars($home->getSubtitle()) ?></h2>
        </div>
        <div id="illustration">
            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($home->getImage2()) ?>" alt="mountain background" class="zoom">
        </div>
        <a href="#description">
            <div id="arrow-down">
                <div class="arrow"></div>
            </div>
        </a>
    </section>

    <section id="description">
        <h2 class="description-title slidingTitle">À propos</h2>
        <div class="description"><?= $home->getDescription(); ?></div>
        <div class="line slidingTitle"></div>
    </section>

    <section id="categories">
        <div class="categories-header" style="background:url(<?= BASE_URL ?>/uploads/<?= htmlspecialchars($home->getImage3()) ?>) no-repeat bottom center;"></div>
        <h2 class="cat-title slidingTitle">Galeries</h2>
        <div class="categories">
            <?php foreach ($categories as $category): ?>
                <a href="<?= BASE_URL ?>/categories.php?id=<?= htmlspecialchars($category->getId()); ?>" class="category-link">
                    <div class="category" style="background:url('<?= BASE_URL ?>/uploads/<?= htmlspecialchars($category->getImage()) ?>') no-repeat; background-size:cover;">
                        <div class="overlay">
                        <svg class="svg2" width="95%" height="95%" xmlns="http://w3.org">
              <rect x="4" y="4" width="calc(100% - 8px)" height="calc(100% - 8px)" fill="transparent" stroke="white" stroke-width="1" pathLength="100" class="draw"/>
                </svg>
                            <h3><?= htmlspecialchars($category->getTitle()) ?></h3>
                            <p><?= htmlspecialchars($category->getDescription()) ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="contact">
        <div id="contact-form">
            <h2 class="contact-title slidingTitle">Contact</h2>
            <form method="post" action="<?= BASE_URL ?>/formulaire.php">
                <input type="hidden" name="sujet" value="contact de votre site" />
                <input type="text" id="nom" name="nom" placeholder="Nom*" /><br><br>
                <input type="text" id="prenom" name="prenom" placeholder="Prenom*" /><br><br>
                <input type="text" id="email" name="email" placeholder="E.mail*" /><br><br>
                <span>Vos commentaires :</span><br><br>
                <input class="remarque" name="remarque" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" placeholder="nom@domaine.com">
                <textarea name="commentaires" rows="8"></textarea><br><br>
                <input type="submit" value="Envoyer" />
                <input type="reset" value="Effacer" />
            </form>
        </div>
        <div id="contact-illustration" style="background:url(<?= BASE_URL ?>/uploads/<?= htmlspecialchars($home->getImage4()) ?>) no-repeat; background-attachment:fixed; background-size:cover;"></div>
    </section>

    <footer></footer>
<?php endforeach; ?>
</main>

<script src="<?= BASE_URL ?>/js/home.js"></script>
</body>
</html>