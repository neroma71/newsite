<?php
require_once __DIR__ . '/../../utils/session_init.php';
require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';

use App\Repository\ActuRepository;
use App\Controller\ActuController;

// Connexion à la base de données via $bdd défini dans db_connect
$actuRepository = new ActuRepository($bdd);
$controller = new ActuController($actuRepository);  

$controller->create();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h2>Créer une actualité</h2>
<form method="post" enctype="multipart/form-data" action="">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <div>
        <label for="title">Titre :</label>
        <input type="text" name="title" id="title">
    </div>
    <div>
        <label for="content">Contenu :</label>
        <textarea name="content" id="content"></textarea>
    </div>
    <div>
        <label for="image">Image :</label>                  
        <input type="file" name="image" id="image" accept="image/*"><br />
        <small>Formats acceptés : jpg, jpeg, png, gif. Taille max : 2 Mo.</small>
    </div>
    <button type="submit">Créer</button>
</form>
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