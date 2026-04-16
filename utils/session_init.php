<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// génération du token csrf
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Expiration session (30 min)
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
    session_unset();
    session_destroy();
    header('Location: /jerome2/public/users/login.php');
    exit;
}
$_SESSION['last_activity'] = time();

// Protection routes
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$publicPages = [
    '/jerome2/public/users/login.php',
    '/jerome2/public/users/register.php'
];

if (!isset($_SESSION['user_id']) && !in_array($currentUri, $publicPages)) {
    header('Location: /jerome2/public/index.php');
    exit;
}