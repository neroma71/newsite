<?php
require_once __DIR__. '/../../utils/session_init.php';
require_once __DIR__. '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__. '/../../utils/db_connect.php';

use App\Repository\ImageRepository;
use App\Repository\ActuRepository;
use App\Controller\ActuController;

$imageRepository = new ImageRepository($bdd);
$actuRepository = new ActuRepository($bdd, $imageRepository);
$controller = new ActuController($actuRepository, $imageRepository);    
$controller->delete((int)$_POST['delete_id']);

$actus = $actuRepository->findAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Actualités</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">  
    <link rel="stylesheet" href="css/manager.css" />
</head>
<body>
     <header>
        <p>Actu manager</p>
     </header>
    <div class="container">
        <p>
         <a href="createActu.php" class="btn btn-primary">Créer une actualité</a>
        </p>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Image</th>
                <th>Modifier</th>
                <th>Supprimer</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($actus as $actu): ?>
            <tr>
                <td><?= htmlspecialchars($actu->getId()) ?></td>
                <td><?= htmlspecialchars($actu->getTitle()) ?></td>
                <td>
                    <?php if ($actu->getImage()): ?>
                        <img src="../../public/uploads/<?= htmlspecialchars($actu->getImage()) ?>" 
                             style="max-width:40px;max-height:40px;" 
                             alt="image">
                    <?php endif; ?>
                </td>
                <td>
                    <a href="editActu.php?id=<?= urlencode($actu->getId()) ?>" class="btn btn-primary">Modifier</a>
                </td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="delete_id" value="<?= htmlspecialchars($actu->getId()) ?>" />
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
        </div>
</body>
</html>