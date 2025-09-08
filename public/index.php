<?php
require_once __DIR__ . '/../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../utils/db_connect.php';
use App\Repository\HomeRepository;
use App\Repository\CategoryRepository;

$homeRepository = new HomeRepository($bdd);
$categoryRepository = new CategoryRepository($bdd);

$homes = $homeRepository->findAll();
$categories = $categoryRepository->findAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main>
        <?php foreach ($homes as $home): ?>
            <header>
                <nav>
                    <ul>
                        <li><a href="#accueil">Accueil</a></li>
                        <li><a href="#description">À propos</a></li>
                        <li><a href="#categories">Catgories</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </nav>
            </header>
             <div id="logo">
                     <?php if ($home->getImage1()): ?>
                        <img src="uploads/<?= htmlspecialchars($home->getImage1()) ?>" alt="Section">
                    <?php endif; ?>
                </div>
            <section id="accueil">
                <div id="title">
                    <h1><?= htmlspecialchars($home->getTitle()) ?></h1>
                    <h2 class="subtitle"><?= htmlspecialchars($home->getSubtitle()) ?></h2>
                </div>
                    <div id="illustration">
                        <img src="./uploads/<?= htmlspecialchars($home->getImage2()) ?>" alt="mountain background" class="zoom">
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
            </section>
            <section id="categories">
              <div class="categories-header" style="background:url(./uploads/<?= htmlspecialchars($home->getImage3()) ?>)no-repeat bottom center;"></div>
                <h2 class="cat-title slidingTitle">Categories</h2>
                <div class="categories">
                    <?php foreach ($categories as $category): ?>
                        <a href="categories.php?id=<?= htmlspecialchars($category->getId()); ?>" class="category-link">
                            <div class="category" style="background:url(./uploads/<?= htmlspecialchars($category->getImage()) ?>)no-repeat; background-size:cover;">
                                <div class="overlay">
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
                        <form method="post" action="formulaire.php">
                            <input type="hidden" name="sujet" value="contact de votre site" />
                            <input type="text" id="nom" name="nom" value="" placeholder="Nom*" />
                            <br /><br />
                            <input type="text" id="prenom" name="prenom" value="" placeholder="Prenom*" />
                            <br /><br />
                            <input type="text" id="email" name="email" value="" placeholder="E.mail*" />
                            <br /><br />
                            <span>Vos commentaires :</span>
                            <br /><br />
                            <input class="remarque" name="remarque" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" placeholder="nom@domaine.com">
                            <textarea name="commentaires" rows="8"></textarea>
                            <br /><br />
                            <input type="submit" value="Envoyer" />
                            <input type="reset" value="Effacer" />	
		                </form>
                    </div>
                    <div id="contact-illustration" style="background:url(./uploads/<?= htmlspecialchars($home->getImage4()) ?>)no-repeat; background-attachment:fixed; background-size:cover;">  
                    </div>     
                </section>
                <footer>
                </footer>
        <?php endforeach; ?>
        
    </main>
     <script src="js/home.js"></script>
</body>
</html>