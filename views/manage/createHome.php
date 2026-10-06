<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/manager.css">
    <title>Document</title>
</head>
<body>
<header>
<p>Créer l'accueil</p>
</header>
<div class="container">
<?php if (isset($errorPostSize)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($errorPostSize) ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" action="" class="formdiv">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <div>
        <label for="title">Titre :</label>
        <input type="text" name="title" id="title" required>
    </div>
    <div>
        <label for="subtitle">Sous titre :</label>
        <input type="text" name="subtitle" id="subtitle" required>
    </div>
    <div>
        <label for="content">Description :</label>
        <textarea name="description" id="content"></textarea>
    </div>
     <small>Formats acceptés : jpg, jpeg, png, gif, webp. Taille max : 2 Mo par image.</small>
<div class="image-grid">
    <div class="formdiv image-item-home">
        <label for="image1">Logo :</label>
        <input type="file" name="image1" id="image1" accept="image/*"><br />
    </div>
    <div class="formdiv image-item-home">
        <label for="image2">Image 2 :</label>
        <input type="file" name="image2" id="image2" accept="image/*"><br />
    </div>
    <div class="formdiv image-item-home">
        <label for="image3">Image 3 :</label>
        <input type="file" name="image3" id="image3" accept="image/*"><br />
    </div>
    <div class="formdiv image-item-home">
        <label for="image4">Image 4 :</label>
        <input type="file" name="image4" id="image4" accept="image/*"><br />
    </div>
</div>
    <button type="submit" class="btn btn-primary">Créer</button>
</form>
</div>
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