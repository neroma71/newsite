<?php

require_once __DIR__ . '/utils/constants.php';
require_once __DIR__ . '/utils/db_connect.php';
require_once __DIR__ . '/utils/autoloader.php';

\Autoloader::register();

use App\Controller\CategoryController;
use App\Controller\ArticleController;
use App\Controller\HomeController;
use App\Controller\ActuController;
use App\Repository\CategoryRepository;
use App\Repository\ArticleRepository;
use App\Repository\ImageRepository;
use App\Repository\HomeRepository;
use App\Repository\ActuRepository;
use App\Service\YoutubeEmbedService;

//dépendances des controllers, repositories et services

$homeRepository = new HomeRepository($bdd);
$categoryRepository = new CategoryRepository($bdd);
$imageRepository = new ImageRepository($bdd);
$actuRepository = new ActuRepository($bdd);
$articleRepository = new ArticleRepository($bdd, $categoryRepository, $imageRepository);

$youtubeService = new YoutubeEmbedService();

$homeController = new HomeController($homeRepository, $categoryRepository);
$categoryController = new CategoryController($categoryRepository, $articleRepository);
$actuController = new ActuController($actuRepository, $homeRepository, $categoryRepository);
$articleController = new ArticleController(
    $articleRepository,
    $imageRepository,
    $categoryRepository,
    $youtubeService
);

  //parsing de l'URL
$requestUri = $_SERVER['REQUEST_URI'];
$path = rawurldecode(parse_url($requestUri, PHP_URL_PATH) ?? '/');

// BASE_URL support
if (defined('BASE_URL') && BASE_URL !== '' && str_starts_with($path, BASE_URL)) {
    $path = substr($path, strlen(BASE_URL));
}

// normalise
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

//routes
$routes = [
    //front office
    '/' => fn() => $homeController->show(),
    '/index.php' => fn() => $homeController->show(),

    '/categories.php' => fn() => $categoryController->show(),

    '/article.php' => fn() => $articleController->show(),

    '/actu.php' => fn() => $actuController->show(),

    //back office
];

  //dispatcher

if (isset($routes[$path])) {
    $routes[$path]();
    exit;
}


  // fichiers statiques
$publicDir = realpath(__DIR__ . '/public');
$target = $publicDir . $path;
$real = realpath($target);

if ($real && str_starts_with($real, $publicDir) && is_file($real)) {

    $ext = strtolower(pathinfo($real, PATHINFO_EXTENSION));

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

    if ($ext === 'php') {
        require $real;
        exit;
    }

    header('Content-Type: application/octet-stream');
    readfile($real);
    exit;
}


//404
http_response_code(404);
require __DIR__ . '/public/404.php';
exit;