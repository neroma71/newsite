<?php
require_once __DIR__ . '/../../utils/session_init.php';
require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';
require_once __DIR__ . '/../../utils/constants.php';

use App\Repository\UsersRepository;

$usersRepository = new UsersRepository($bdd);

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    session_destroy();
    header('Location: /newsite/views/front/users/login.php');
    exit;
}

$user = $usersRepository->findById((int)$userId);

if (!$user) {
    session_destroy();
    header('Location: /newsite/views/front/users/login.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/manager.css">
</head>
<body>
    <header>
        <h2 id="dashboard-title">Bienvenue sur votre dashboard !</h2>
    </header>
        <section class="dashboard">
            <p>Voici votre tableau de bord où vous pouvez gérer vos activités.</p>
                <div class="actions">
                    <div class="action-card">
                        <a href="homeManager.php">Gérer l'accueil</a>
                    </div>  
                    <div class="action-card">
                        <a href="category.php">Gérer les catégories</a>
                    </div>  
                    <div class="action-card">
                        <a href="articleManager.php">Gérer les articles</a>
                    </div>
                    <div class="action-card">
                        <a href="<?= BASE_URL ?>/manage/actus">Gérer les actus</a>
                    </div>     
                </div>
        </section>
</body>
</html>
