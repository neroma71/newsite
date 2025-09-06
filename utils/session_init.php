<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // à activer uniquement si HTTPS
ini_set('session.use_strict_mode', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Génération du token CSRF si absent
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Redirige si pas connecté et si accès à une page protégée
$publicPages = ['/users/login.php', '/users/register.php'];
$currentUri = $_SERVER['REQUEST_URI'];

if (!isset($_SESSION['user_id']) && !in_array($currentUri, $publicPages)) {
    header('Location: /jerome2/public/index.php');
    exit;
}