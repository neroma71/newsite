<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// timeout session
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
    session_unset();
    session_destroy();
    header('Location: /newsite/views/users/login.php');
    exit;
}

$_SESSION['last_activity'] = time();

/**
 * Guard admin
 */
function requireAuth(): void
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // protège toute la zone admin
    if (str_starts_with($path, '/newsite/manage')) {
        if (empty($_SESSION['user_id'])) {
            header('Location: /newsite/views/users/login.php');
            exit;
        }
    }
}