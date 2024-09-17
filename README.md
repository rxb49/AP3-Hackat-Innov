# Plateforme Hackat'innov Hackathon

## Introduction

Hackat'innov est une plateforme web conçue pour gérer les hackathons, de l'inscription des participants à la gestion des équipes et au suivi des événements hackathon. Construite avec Laravel, Vue.js et d'autres technologies web modernes, elle offre une solution complète pour organiser et exécuter des événements hackathon.

## Fonctionnalités

- **Gestion de Hackathon** : Créer et gérer des événements hackathon, y compris les dates, les thèmes et les objectifs.
- **Gestion d'Équipe** : Création d'équipes, permettant aux participants de rejoindre des équipes existantes ou de former de nouvelles équipes.
- **Gestion des Participants** : Gérer les inscriptions des participants, y compris les informations personnelles et les affectations d'équipe.
- **Mises à Jour en Temps Réel** : Utilisation de Vue.js pour les mises à jour dynamiques du contenu, fournissant un retour et une interaction en temps réel.
- **Documentation de l'API** : Accès à une documentation OpenAPI complète pour l'intégration avec des services tiers ou pour référence des développeurs.

## Installation

### Prérequis

- PHP >= 8.3
- Composer
- Laravel >= 11.x
- MySQL ou tout système de base de données compatible

### Étapes

1. Cloner le dépôt Git :
2. Installer les dépendances PHP avec Composer : `composer install`
3. Créer un fichier `.env` à partir du modèle `.env.example`.
4. Générer une clé d'application Laravel : `php artisan key:generate`
5. Configurer les informations de base de données dans le fichier `.env` (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
6. Lancer le projet avec Laravel : `php artisan serve`

## Documentation

La documentation de l'API est disponible au format YAML. Elle est consultable dans le dossier `public/documentation.yaml` via :

- [Swagger Editor](https://editor.swagger.io/)
- Via PHPStorm qui propose une visualisation de la documentation OpenAPI

## Déploiement

Pour installer la plateforme sur un serveur, deux solutions sont possibles :

- Via Docker : https://cours.brosseau.ovh/tp/ops/deployer-laravel-docker.html
- Via un serveur Apache classique : https://cours.brosseau.ovh/tp/ops/deployer-laravel.html

## Contributeurs

- Valentin Brosseau
- Carine Autret
