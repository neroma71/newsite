<?php
require_once __DIR__ . '/../../utils/session_init.php';
require_once __DIR__ . '/../../utils/autoloader.php';
Autoloader::register();
require_once __DIR__ . '/../../utils/db_connect.php';

use App\Repository\UsersRepository;

$usersRepository = new UsersRepository($bdd);
$user = $usersRepository->findById($_SESSION['user_id']);

if (!$user) {
    session_destroy();
    header('Location: /jerome2/views/users/login.php');
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
                        <a href="manager.php">Gérer l'accueil</a>
                    </div>  
                    <div class="action-card">
                        <a href="category.php">Gérer les catégories</a>
                    </div>  
                    <div class="action-card">
                        <a href="articlemanager.php">Gérer les articles</a>
                    </div>   
                </div>
        </section>
</body>
</html>
