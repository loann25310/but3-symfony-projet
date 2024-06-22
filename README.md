# Application de Gestion d'Événements
## Description du Projet

Cette application web Symfony permet aux utilisateurs de créer, gérer, s'inscrire et participer à des événements. Elle offre une interface conviviale pour la gestion complète du cycle de vie des événements, de leur création à leur participation.
## Fonctionnalités Principales

* Création d'événements : Permet aux utilisateurs de créer de nouveaux événements avec des détails complets tels que la date, l'heure, la localisation, et une description.

* Gestion des événements : Les organisateurs peuvent modifier et supprimer leurs événements existants.

* Inscription aux événements : Les utilisateurs peuvent s'inscrire pour participer à des événements créés par d'autres utilisateurs.

* Gestion des utilisateurs : Authentification et autorisation des utilisateurs avec des rôles spécifiques pour les organisateurs et les participants.

## Installation
```bash
composer install
php bin/console doctrine:database:create
php bin/console doc:sc:up -f
php bin/console doctrine:fixtures:load
yarn install
yarn build
symfony server:start 
```

## Auteurs
* Félix RIAT
* Loann LAGARDE
