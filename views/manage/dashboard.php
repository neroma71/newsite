<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/manager.css">
</head>
<body>
    <header>
        <h2 id="dashboard-title">Bienvenue sur votre dashboard !</h2>
    </header>
        <section class="dashboard">
            <p>Voici votre tableau de bord où vous pouvez gérer vos activités.</p>
                <div class="actions">
                    <div class="action-card">
                        <a href="<?= BASE_URL ?>/manage/home">Gérer l'accueil</a>
                    </div>  
                    <div class="action-card">
                        <a href="<?= BASE_URL ?>/manage/categories">Gérer les catégories</a>
                    </div>  
                    <div class="action-card">
                        <a href="<?= BASE_URL ?>/manage/articles">Gérer les articles</a>
                    </div>
                    <div class="action-card">
                        <a href="<?= BASE_URL ?>/manage/actus">Gérer les actus</a>
                    </div>     
                </div>
        </section>
</body>
</html>
