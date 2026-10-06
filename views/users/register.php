<?php
/**
 * @var array $errors
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/register.css">
</head>
<body>
    <div class="container">
        <h2>Inscription</h2>
         <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"> 
                <?php foreach ($errors as $error): ?> 
                    <p><?= htmlspecialchars($error) ?></p> 
                <?php endforeach; ?> 
            </div> 
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>/users/register">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="8">
            </div>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>     
    </div>
</body>   
</html>