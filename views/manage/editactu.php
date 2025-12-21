<?php
require_once __DIR__ . '/../../utils/session_init.php';

require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';

use App\Repository\ImageRepository;
use App\Repository\ActuRepository;
use App\Controller\ActuController;

$csrfToken = $_SESSION['csrf_token'];

// Récupération de l'ID de l'actualité à modifier
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;    
$actuRepository = new ActuRepository($bdd);
$imageRepository = new ImageRepository($bdd);

$controller = new ActuController($actuRepository, $imageRepository);
$actu = $controller->update($id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'actualité</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">            
    <link rel="stylesheet" href="css/manager.css" />
</head>
<body>
    <header>
        <p>Modifier l'actualité</p>
    </header>
    <div class="container">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="formdiv">
                <p><label for="title">Titre :</label></p>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($actu->getTitle()) ?>" required> 
            </div>
        <div class="formdiv">
                <p><label for="content">Contenu :</label></p>
                <textarea name="content" id="content" required><?= htmlspecialchars($actu->getContent()) ?></textarea>
            </div>
        <div class="formdiv">
                <p><label for="image">Image (laisser vide pour conserver l'image actuelle) :</label></p>
                <input type="file" name="image" id="image" accept="image/*">
            </div>
            <div class="formdiv">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
ClassicEditor
    .create(document.querySelector('#content'), {
        htmlSupport: {
            allow: [
                {
                    name: /.*/, // autorise toutes les balises…
                    attributes: true,
                    classes: true,
                    styles: true
                }
            ],
            disallow: [
                {
                    name: 'script' // …sauf <script>
                },
            ]
        }
    })
    .catch(error => {
        console.error(error);
    });
</script>
</body>
</html>