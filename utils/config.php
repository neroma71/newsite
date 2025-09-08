<?php
//centralisation des liens
// Chemin physique vers le dossier public (pour les fichiers côté serveur)
define('PUBLIC_PATH', __DIR__ . '/public');

// URL publique (pour les liens dans les vues)
define('BASE_URL', '/newsite'); // Ici on enlève 'public' pour la réécriture

// Chemin vers uploads
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');
define('UPLOADS_URL', BASE_URL . '/uploads');