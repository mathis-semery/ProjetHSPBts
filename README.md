# GDH Console — Plateforme de Gestion Hospitalière et de Mise en Réseau Professionnel

> Projet de fin de 2ème année — BTS Services Informatiques aux Organisations (SIO)
> Option SLAM — Solutions Logicielles et Applications Métiers

---

## Présentation du projet

**GDH Console** est une application web développée dans le cadre du projet de fin de formation BTS SIO 2ème année. Elle répond à un besoin concret de mise en réseau entre établissements de santé, étudiants en formation médicale et entreprises partenaires.

La plateforme propose :

- La **gestion des offres d'emploi et de stage** avec un système de candidature intégré
- La **gestion d'événements** (conférences, formations, journées portes ouvertes) avec inscription en ligne
- Un **espace de discussion communautaire** (forums par canaux thématiques)
- La **gestion des utilisateurs** avec un système de rôles et de vérification des comptes
- L'**administration des structures** : hôpitaux, établissements d'enseignement, entreprises

---

## Stack technique

| Technologie | Version | Rôle |
|---|---|---|
| PHP | 8.2+ | Langage back-end |
| Symfony | 7.3 | Framework MVC |
| Doctrine ORM | 3.x | Couche d'accès aux données |
| MySQL / MariaDB | 8.x | Base de données relationnelle |
| Twig | 3.x | Moteur de templates |
| Bootstrap (Keenthemes) | 5.x | Framework CSS / UI |
| Symfony UX Turbo | — | Navigation sans rechargement complet |
| Symfony Stimulus | — | JavaScript léger côté client |
| PHPUnit | 11.x | Tests unitaires |
| Composer | 2.x | Gestionnaire de dépendances PHP |

---

## Architecture du projet

Le projet suit le pattern **MVC (Modèle - Vue - Contrôleur)** imposé par Symfony :

```
ProjetHSPBts/
├── config/              # Configuration de l'application (services, routes, sécurité)
├── migrations/          # Migrations de base de données (Doctrine Migrations)
├── public/              # Point d'entrée web (index.php, assets publics)
│   └── telecharger/cv/  # CV téléchargés par les utilisateurs
├── src/
│   ├── Controller/      # Contrôleurs HTTP (logique métier)
│   ├── Entity/          # Entités Doctrine (modèles de données)
│   ├── Form/            # Formulaires Symfony
│   ├── Repository/      # Requêtes Doctrine personnalisées
│   └── Service/         # Services métier (envoi d'emails, notifications)
├── templates/           # Vues Twig (HTML)
└── tests/               # Tests unitaires PHPUnit
```

---

## Modèle de données

### Entités principales

| Entité | Description |
|---|---|
| `User` | Utilisateur avec rôle, CV, profession, formation et token de vérification |
| `Offre` | Offre d'emploi ou de stage publiée par un partenaire |
| `Candidature` | Candidature d'un utilisateur à une offre, avec lettre de motivation |
| `Evenement` | Événement avec dates, lieu, capacité et nombre de places disponibles |
| `Inscription` | Inscription d'un utilisateur à un événement |
| `Canal` | Canal de discussion thématique (forum) |
| `Post` | Message posté dans un canal |
| `Reponse` | Réponse à un post (commentaire ou réponse imbriquée) |
| `Hopital` | Structure hospitalière |
| `Etablissement` | Établissement d'enseignement |
| `Entreprise` | Entreprise partenaire |

### Gestion des rôles

| Rôle | Description |
|---|---|
| `ROLE_ADMIN` | Accès total — validation des comptes, gestion des structures |
| `ROLE_ELEVE` | Étudiant — consultation des offres, candidatures, inscriptions aux événements |
| `ROLE_MEDECIN` | Professionnel de santé — mêmes droits que l'élève + accès spécifique |
| `ROLE_PARTENAIRE` | Entreprise/partenaire — publication d'offres, consultation des candidatures |
| `ROLE_ATTENTE_VERIFICATION` | Compte en attente de validation par un administrateur |

---

## Fonctionnalités détaillées

### Authentification & Gestion des comptes
- Inscription avec choix de rôle et upload de CV
- Vérification d'email via lien sécurisé (`symfonycasts/verify-email-bundle`)
- Réinitialisation de mot de passe par email (`symfonycasts/reset-password-bundle`)
- Validation manuelle des comptes par un administrateur
- Modification du profil et du CV

### Offres & Candidatures
- Publication d'offres (titre, description, mission, type, salaire) par les partenaires
- Candidature en ligne avec lettre de motivation et envoi automatique d'un email au partenaire
- Gestion des candidatures (accepter / refuser) par le partenaire ou l'admin
- Consultation de l'historique de ses candidatures

### Événements & Inscriptions
- Création d'événements avec dates de début/fin, lieu, capacité maximale et liste du matériel requis
- Inscription en ligne avec confirmation de présence
- Suivi du nombre de places disponibles en temps réel

### Espace communautaire (Forums)
- Création de canaux de discussion thématiques
- Publication de posts et de réponses imbriquées
- Système d'abonnement et de notifications automatiques par email

### Administration
- Tableau de bord des comptes en attente de vérification
- CRUD complet sur les hôpitaux, établissements et entreprises
- Envoi de notifications email aux administrateurs avec pièces jointes (dossiers de vérification)

---

## Installation & Démarrage

### Prérequis

- PHP 8.2 ou supérieur
- Composer
- MySQL / MariaDB
- Node.js & npm (pour les assets front-end)
- Un serveur local (WampServer, XAMPP, Laragon ou Symfony CLI)

### Étapes d'installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/<votre-compte>/ProjetHSPBts.git
   cd ProjetHSPBts
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Configurer les variables d'environnement**

   Copier le fichier `.env` et adapter les paramètres :
   ```bash
   cp .env .env.local
   ```
   Modifier `.env.local` :
   ```dotenv
   DATABASE_URL="mysql://root:@127.0.0.1:3306/gdh_console?serverVersion=8.0"
   MAILER_DSN=smtp://localhost:1025
   NOM_DOMAINE=localhost
   MAIL_FROM=noreply@gdh-console.local
   ```

4. **Créer la base de données et jouer les migrations**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Créer un compte administrateur**
   ```bash
   php bin/console app:create-user
   ```
   *(ou insérer manuellement un utilisateur avec `ROLE_ADMIN` en base)*

6. **Installer les assets front-end**
   ```bash
   npm install
   npm run build
   ```

7. **Démarrer le serveur de développement**
   ```bash
   symfony serve
   # ou
   php -S localhost:8000 -t public/
   ```

   L'application est accessible sur `http://localhost:8000`

---

## Tests

Les tests unitaires sont écrits avec **PHPUnit** :

```bash
php bin/phpunit
```

---

## Sécurité

- Mots de passe hachés avec `bcrypt` via le composant `symfony/password-hasher`
- Protection CSRF sur tous les formulaires
- Contrôle d'accès par rôle sur chaque route (`access_control` dans `security.yaml`)
- Validation des fichiers uploadés (type MIME, taille)
- Tokens signés pour la vérification d'email et la réinitialisation de mot de passe

---

## Auteurs

Projet réalisé par des étudiants de BTS SIO 2ème année dans le cadre des épreuves de fin de formation.

---

## Licence

Ce projet est développé à des fins pédagogiques dans le cadre du BTS SIO.
