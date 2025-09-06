<?php
$dns = "mysql:host=localhost;dbname=jerome2;charset=utf8mb4";
$user = "root";
$password = "root";

try {
    $bdd = new PDO($dns, $user, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}