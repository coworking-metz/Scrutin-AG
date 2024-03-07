# Outil de vote pour l'AG de l'Association Coworking Metz

Ce dépôt contient l'ensemble des fichiers nécessaires à la mise en place d'un système de vote pour l'élection du conseil d'administration de l'Association Coworking Metz. 
Le système permet de gérer les candidatures, les votes, le dépouillement, et l'affichage des résultats. Il s'agit d'une brique à inclure dans wordpress

## Structure des fichiers

- `depouillement.inc.php` : Contient les fonctions relatives au dépouillement des votes, à l'affichage des résultats et à la vérification de l'état du dépouillement.
- `formulaire.inc.php` : Gère l'affichage du formulaire de vote, la redirection des utilisateurs non connectés, et l'affichage des candidats selon l'état du scrutin.
- `log.inc.php` : Fournit les fonctions de log pour enregistrer les actions des utilisateurs et les erreurs dans un service externe.
- `main.inc.php` : Inclut tous les fichiers nécessaires au fonctionnement du système de vote et ajoute les CSS et JS nécessaires.
- `reglages.inc.php` : Récupère les paramètres de l'assemblée générale depuis la page d'options ACF.
- `users.inc.php` : Contient les fonctions relatives aux utilisateurs et candidats, notamment la récupération des utilisateurs candidats et électeurs.
- `vote.css` : Styles CSS pour le formulaire de vote et l'affichage des candidats.
- `vote.inc.php` : Gère le processus de vote, l'enregistrement des votes, et la vérification des conditions de vote.
- `vote.js` : Script JavaScript pour l'interface de vote, gérant la sélection des candidats et la soumission du formulaire.

## Fonctionnalités

- **Gestion des candidatures** : Permet aux utilisateurs de se présenter comme candidats au conseil d'administration.
- **Système de vote** : Offre une interface de vote sécurisée et anonyme pour les membres de l'association.
- **Dépouillement automatique** : Automatise le dépouillement des votes et l'affichage des résultats.
- **Logs des actions** : Enregistre les actions des utilisateurs et les erreurs dans un service externe pour un suivi facile.
- **Adaptabilité** : Le système est conçu pour être facilement adaptable à d'autres types d'élections ou de scrutins.

## Installation

1. Clonez le dépôt du site wordpress et cloner ensuite ce dépot dans mu-plugins.
2. Incluez `main.inc.php` dans votre projet pour charger automatiquement tous les fichiers nécessaires.

## Contribution

Les contributions au projet sont les bienvenues. Pour contribuer, veuillez forker le dépôt, créer une branche pour votre fonctionnalité ou correction, et soumettre une pull request.

