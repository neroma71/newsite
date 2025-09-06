<?php
require_once __DIR__ . '/../../utils/session_init.php';
require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';
use App\Repository\HomeRepository;
use App\Controller\HomeController;

$homeRepository = new HomeRepository($bdd);
$controller = new HomeController($homeRepository);

// Récupérer l'id de la home à modifier
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$errors = [];
// Traitement du formulaire si POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->update($id, $errors);
}

// On récupère toujours l'objet Home pour affichage
$home = $id ? $homeRepository->findById($id) : null;

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Modifier l'accueil</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
        <link rel="stylesheet" href="css/manager.css">
    </head>
<body>
<h2>Modifier l'accueil</h2>
<?php if ($home): ?>
<div class="container">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <div>
        <label for="title">Titre :</label>
        <p>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($home->getTitle()) ?>" required>
        </p>
    </div>
    <div>
        <label for="subtitle">Sous titre :</label>
        <p>
        <input type="text" name="subtitle" id="subtitle" value="<?= htmlspecialchars($home->getSubtitle()) ?>" required>
        </p>
    </div>
    <div>
        <label for="content">Description :</label>
        <p>
        <textarea name="description" id="content"><?= htmlspecialchars($home->getDescription()) ?></textarea>
        </p>
    </div>
    <p>Formats acceptés : jpg, jpeg, png, gif. Taille max : 2 Mo par image.</p>
<div class="image-grid">
    <div class="formdiv image-item">
        <label for="image1">Logo :</label>
        <p>
        <?php if ($home->getImage1()): ?>
            <img src="../../public/uploads/<?= htmlspecialchars($home->getImage1()) ?>" style="max-width:100px;max-height:100px;" alt="Image 1 actuelle"><br>
            <label>
                <input type="checkbox" name="delete_image1" value="1">
                Supprimer l'image
            </label>
        <?php endif; ?>
        </p>
        <label>Modifier l'image</label>
        <input type="file" name="image1" id="image1" accept="image/*"><br />
    </div>
    <div class="formdiv image-item">
        <label for="image2">Image 2 :</label>
        <p>
        <?php if ($home->getImage2()): ?>
            <img src="../../public/uploads/<?= htmlspecialchars($home->getImage2()) ?>" style="max-width:100px;max-height:100px;" alt="Image 2 actuelle"><br>
            <label>
                <input type="checkbox" name="delete_image2" value="1">
                Supprimer l'image
            </label>
        <?php endif; ?>
        </p>
        <label>Modifier l'image</label>
        <input type="file" name="image2" id="image2" accept="image/*"><br />
    </div>
    <div class="formdiv image-item">
        <label for="image3">Image 3 :</label>
        <p>
        <?php if ($home->getImage3()): ?>
            <img src="../../public/uploads/<?= htmlspecialchars($home->getImage3()) ?>" style="max-width:100px;max-height:100px;" alt="Image 3 actuelle"><br>
            <label>
                <input type="checkbox" name="delete_image3" value="1">
                Supprimer l'image
            </label>
        <?php endif; ?>
        </p>
        <label>Modifier l'image</label>
        <input type="file" name="image3" id="image3" accept="image/*"><br />
    </div>
    <div class="formdiv image-item">
        <label for="image4">Image 4 :</label>
        <p>
        <?php if ($home->getImage4()): ?>
            <img src="../../public/uploads/<?= htmlspecialchars($home->getImage4()) ?>" style="max-width:100px;max-height:100px;" alt="Image 4 actuelle"><br>
            <label>
                <input type="checkbox" name="delete_image4" value="1">
                Supprimer l'image
            </label>
        <?php endif; ?>
        </p>
        <label>Modifier l'image</label>
        <input type="file" name="image4" id="image4" accept="image/*"><br />
    </div>
</div>
    <br />
    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
</form>
    <?php else: ?>
    <p>Accueil introuvable.</p>
    <?php endif; ?>
</div>
<footer></footer>
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
