<?php
require_once __DIR__ . '/../../utils/session_init.php';
require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';
use App\Repository\HomeRepository;
use App\Controller\HomeController;

$homeRepository = new HomeRepository($bdd);
$controller = new HomeController($homeRepository);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $controller->delete((int)$_POST['delete_id'], $_POST['csrf_token'] ?? '');
    } catch (\Exception $e) {
        echo "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

$homes = $homeRepository->findAll();
?>
<html>
<head>
    <title>Gestion de la pages d'accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="css/manager.css">
</head>
<body>
<div class="container">
    <h1>Gestion de la pages d'accueil</h1>
    <p><a href="createHome.php" class="btn btn-primary">Créer l'accueil</a></p>
<table class="table mt-5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Sous titre</th>
            <th>Description</th>
            <th>Images</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($homes as $home): ?>
        <tr>
            <td><?= htmlspecialchars($home->getId()) ?></td>
            <td><?= htmlspecialchars($home->getTitle()) ?></td>
            <td><?= htmlspecialchars($home->getSubtitle()) ?></td>
            <td>
                <?php
                $desc = strip_tags($home->getDescription());
                $short = mb_substr($desc, 0, 50);
                if (mb_strlen($desc) > 50) {
                    $short .= '...';
                }
                echo htmlspecialchars($short);
                ?>
            </td>
            <td>
                <?php for ($i = 1; $i <= 4; $i++): 
                    $img = $home->{'getImage'.$i}();
                    if ($img): ?>
                        <img src="../../public/uploads/<?= htmlspecialchars($img) ?>" style="max-width:40px;max-height:40px;" alt="img<?= $i ?>">
                    <?php endif;
                endfor; ?>
            </td>
            <td>
                <button class="btn btn-primary">
                <a href="editHome.php?id=<?= $home->getId() ?>">Éditer</a>
                </button>
                <form method="post" action="manager.php" style="display:inline;">
                    <input type="hidden" name="delete_id" value="<?= $home->getId() ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <button type="submit" class="btn btn-primary">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</body>
</html>
