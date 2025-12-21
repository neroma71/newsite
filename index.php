<?php
require_once __DIR__ . '/utils/constants.php';

$publicDir = realpath(__DIR__ . '/public');
$requestUri = $_SERVER['REQUEST_URI'];
$path = rawurldecode(parse_url($requestUri, PHP_URL_PATH) ?? '/');

// Retire le préfixe BASE_URL si présent (ex: /newsite)
if (defined('BASE_URL') && BASE_URL !== '' && strpos($path, BASE_URL) === 0) {
    $path = substr($path, strlen(BASE_URL));
    if ($path === '') {
        $path = '/';
    }
}

// Retirer un éventuel préfixe /public dans l'URL
$path = preg_replace('~^/public~', '', $path);

// Normaliser
if ($path === '') {
    $path = '/';
}

// Si racine -> servir public/index.php
if ($path === '/' || $path === '/index.php') {
    require $publicDir . '/index.php';
    exit;
}

// Construire chemin réel dans /public
$target = $publicDir . $path;
$real = realpath($target);

// Vérifier que le fichier demandé est bien dans le dossier public
if ($real && strpos($real, $publicDir) === 0 && is_file($real)) {
    $ext = strtolower(pathinfo($real, PATHINFO_EXTENSION));

    // Fichiers statiques : servir directement avec bon Content-Type
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'svg'  => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject'
    ];

    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($real);
        exit;
    }

    // Fichiers PHP : exécuter
    if ($ext === 'php') {
        require $real;
        exit;
    }

    // Autres fichiers : forcer le téléchargement / affichage binaire
    header('Content-Type: application/octet-stream');
    readfile($real);
    exit;
}

// Si rien ne correspond -> 404
header("HTTP/1.0 404 Not Found");
echo "404 Not Found";
exit;
