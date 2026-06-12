# newsite
un nouveau site

## Présentation
Projet personnel de site web développé en PHP sans framework afin de comprendre en profondeur le fonctionnement du MVC, du routage, des repositories et de la gestion des données.
Ce projet est en cours d'évolution et donc va encore changer.

## Stack
- PHP (POO)
- MySQL / PDO
- HTML / CSS
- JavaScript (léger)
- Architecture MVC maison

## Objectifs
- Comprendre le fonctionnement interne d’un framework MVC
- Gérer un routing manuel
- Implémenter un système de repositories
- Gérer les uploads d’images
- Sécuriser les formulaires (CSRF)
- Créer une séparation front / back office

## Architecture
Le projet est basé sur une architecture MVC simplifiée :

- Controllers : logique métier
- Repositories : accès base de données
- Entities : objets métier
- Views : affichage
- Services : logique réutilisable (upload, embed YouTube)
- Router : gestion des routes

## Fonctionnalités
- CRUD articles / catégories / actualités
- Upload multiple d’images
- Gestion des catégories
- Pagination
- Système d’administration
- Affichage public dynamique
- Intégration YouTube dans les contenus

## Sécurité
- Protection CSRF sur les formulaires
- Validation des uploads
- Requêtes PDO préparées

## Ce que ce projet m’a permis d’apprendre
- Architecture MVC sans framework
- Gestion de routing personnalisé
- Séparation des responsabilités
- Hydratation d’objets
- Gestion des fichiers en PHP
