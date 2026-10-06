```php
<?php
/** @var \App\Entity\Home[] $homes */
?>
<html>
<head>
    <title>Gestion de la page d'accueil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
          crossorigin="anonymous">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/manager.css">
</head>
<body>
<header>
    <p>Gestion de la page d'accueil</p>
</header>
<div class="container">
    <p>
        <a href="<?= BASE_URL ?>/manage/home/create" class="btn btn-primary">
            Créer l'accueil
        </a>
    </p>
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
                <td>
                    <?= htmlspecialchars($home->getId()) ?>
                </td>
                <td>
                    <?= htmlspecialchars($home->getTitle()) ?>
                </td>
                <td>
                    <?= htmlspecialchars($home->getSubtitle()) ?>
                </td>
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
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <?php $img = $home->{'getImage' . $i}();?>
                        <?php if ($img): ?>
                            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($img) ?>" style="max-width:40px;max-height:40px;" alt="img<?= $i ?>">
                        <?php endif; ?>
                    <?php endfor; ?>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>/manage/home/edit?id=<?= (int) $home->getId() ?>" class="btn btn-primary">Éditer</a>
                    <form method="post" action="<?= BASE_URL ?>/manage/home/delete" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= (int) $home->getId() ?>">
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
