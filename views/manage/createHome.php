<?php
require_once __DIR__ . '/../../utils/session_init.php';
require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';
use App\Repository\HomeRepository;
use App\Controller\HomeController;

// Gestion du cas où la taille du POST dépasse la limite PHP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)) {
    $errorPostSize = "Une ou plusieurs images sont trop volumineuses (max 2 Mo par image, 8 Mo total).";
} else {
    // Connexion à la base de données via $bdd défini dans db_connect
   $homeRepository = new HomeRepository($bdd);
   $controller = new HomeController($homeRepository);

    $controller->create();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h2>Créer l'accueil</h2>
<?php if (isset($errorPostSize)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($errorPostSize) ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" action="">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <div>
        <label for="title">Titre :</label>
        <input type="text" name="title" id="title" required>
    </div>
    <div>
        <label for="subitle">Sous titre :</label>
        <input type="text" name="subtitle" id="subtitle" required>
    </div>
    <div>
        <label for="content">Description :</label>
        <textarea name="description" id="content"></textarea>
    </div>
    <div>
        <label for="image1">Logo :</label>
        <input type="file" name="image1" id="image1" accept="image/*"><br />
    </div>
    <div>
        <label for="image2">Image 2 :</label>
        <input type="file" name="image2" id="image2" accept="image/*"><br />
    </div>
    <div>
        <label for="image3">Image 3 :</label>
        <input type="file" name="image3" id="image3" accept="image/*"><br />
    </div>
    <div>
        <label for="image4">Image 4 :</label>
        <input type="file" name="image4" id="image4" accept="image/*"><br />
        <small>Formats acceptés : jpg, jpeg, png, gif. Taille max : 2 Mo par image.</small>
    </div>
    <button type="submit">Créer</button>
</form>
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