# Charrak TECHNOLOGIE — Application de Gestion Commerciale & Stock

## 1_ Présentation

**Charrak TECHNOLOGIE** est une application web complète destinée aux artisans, techniciens et petites entreprises de services qui ont besoin de :

- Suivre leurs **clients** et l'historique des services réalisés
- Gérer leur **catalogue de produits** et **catégories**
- Gérer le **stock automatiquement** : chaque produit utilisé lors d'un service est automatiquement déduit du stock
- Générer des **factures PDF** par service et des **rapports mensuels PDF**
- Avoir un **tableau de bord** avec statistiques en temps réel

---

## 2_ Fonctionnalités

### Tableau de bord
- Statistiques globales (clients, CA mensuel, services, alertes stock)
- 5 derniers services avec liens rapides
- Alertes stock faible en temps réel
- Top 5 clients par chiffre d'affaires
- Derniers mouvements de stock

### Gestion des Clients
- Ajouter / Modifier / Supprimer un client
- Recherche par nom, téléphone ou adresse
- Fiche client détaillée avec historique des services
- Statistiques par client (nombre de services, total dépensé)

### Gestion des Catégories
- CRUD complet des catégories de produits
- Compteur de produits par catégorie
- Protection suppression si catégorie utilisée

### Gestion des Produits
- CRUD complet avec catégorie associée
- Suivi du stock en temps réel avec alertes visuelles
- Seuil d'alerte personnalisable par produit
- Réapprovisionnement rapide directement depuis la liste
- 3 statuts : Normal / Faible / Rupture (avec code couleur)
- Historique complet des mouvements par produit

### Historique des Services
- Enregistrement d'un service avec sélection client + produits
- **Décrémentation automatique du stock** lors de la création
- Calcul automatique du montant total
- Filtres par client, mois, année et statut
- Détail complet de chaque service (produits, quantités, prix)
- **Impression PDF de la facture** par service
- Suppression avec restauration automatique du stock

### Gestion du Stock
- État du stock en temps réel avec valeur totale
- Historique de tous les mouvements (entrées/sorties)
- Filtres par produit, type de mouvement, période
- Réapprovisionnement avec enregistrement du motif
- Lien entre mouvement de stock et service client

### Export PDF
- Facture professionnelle par service
- Rapport mensuel complet avec récapitulatif par client
- Filtres par mois, année et client

---

## 3_ Technologies utilisées

| Composant       | Technologie            | Version |
|-----------------|------------------------|---------|
| Framework       | Laravel                | 12.x    |
| Langage         | PHP                    | 8.5+    |
| Base de données | MySQL                  | 8.0+    |
| Frontend        | Blade + Bootstrap      | 5.3     |
| Icônes          | Bootstrap Icons        | 1.11    |
| PDF             | barryvdh/laravel-dompdf| 2.x     |
| Auth            | Laravel Breeze (custom)| —       |
| Typo            | Plus Jakarta Sans      | Google  |

---

## 4_ Architecture du projet

```
gestion-commerciale/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/
│   │       │   └── AuthenticatedSessionController.php
│   │       ├── DashboardController.php
│   │       ├── ClientController.php
│   │       ├── CategorieController.php
│   │       ├── ProduitController.php
│   │       ├── HistoriqueController.php
│   │       └── StockController.php
│   └── Models/
│       ├── User.php
│       ├── Client.php
│       ├── Categorie.php
│       ├── Produit.php
│       ├── Historique.php
│       ├── DetailHistorique.php
│       └── GestionStock.php
│
├── database/
│   ├── migrations/
│   │   ├── ..._create_users_table.php
│   │   ├── ..._create_clients_table.php
│   │   ├── ..._create_categories_table.php
│   │   ├── ..._create_produits_table.php
│   │   ├── ..._create_historiques_table.php
│   │   ├── ..._create_detail_historiques_table.php
│   │   └── ..._create_gestion_stocks_table.php
│   ├── seeders/
│   │   └── DatabaseSeeder.php
│   └── gestion_commerciale.sql   ← Script SQL complet
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php         ← Layout principal (sidebar + topbar)
│       ├── auth/
│       │   └── login.blade.php       ← Page de connexion
│       ├── dashboard/
│       │   └── index.blade.php       ← Tableau de bord
│       ├── clients/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       ├── categories/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── produits/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       ├── historique/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── show.blade.php
│       │   └── mensuel.blade.php
│       ├── stock/
│       │   ├── index.blade.php       ← Mouvements
│       │   └── etat.blade.php        ← Inventaire
│       └── pdf/
│           ├── facture.blade.php
│           └── historique-mensuel.blade.php
│
└── routes/
    ├── web.php
    └── auth.php
```

---

## 5_ Installation

### Prérequis

- PHP >= 8.5
- Composer
- MySQL 8.0+
- Node.js (optionnel, pour assets)
- Serveur Apache/Nginx ou Laravel Sail

### Étape 1 — Cloner et installer

```bash
# Cloner le projet
git clone https://github.com/votre-user/gestion-commerciale.git
cd gestion-commerciale

# Installer les dépendances PHP
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

### Étape 2 — Configurer l'environnement

Editez le fichier `.env` :

```env
APP_NAME="Gestion Commerciale"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_commerciale
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### Étape 3 — Base de données

```bash
# Exécuter les migrations
php artisan migrate

# Peupler avec les données de test (27 produits, 12 clients, 25+ services)
php artisan db:seed
```

### Étape 4 — Lancer le serveur

```bash
php artisan serve
```

Accédez à : **http://localhost:8000**

---

## 6_ Connexion

| Champ | Valeur           |
|-------|------------------|
| Email | `admin@gmail.ma` |
| Mot de passe | `password`|

---

## 7_ Configuration de la base de données

### Schéma relationnel

```
users
├── id, name, email, password

clients
├── id_client (PK)
├── nom, telephone, adresse, date_creation

categories
├── id_categorie (PK)
├── nom_categorie, description

produits
├── id_produit (PK)
├── id_categorie (FK → categories)
├── nom_produit, prix_unitaire
├── quantite_stock, seuil_alerte

historiques
├── id_historique (PK)
├── id_client (FK → clients)
├── date_service, montant_total, remarque, statut

detail_historiques
├── id_detail (PK)
├── id_historique (FK → historiques)
├── id_produit (FK → produits)
├── quantite_utilisee, prix_unitaire, prix_total

gestion_stocks
├── id_stock (PK)
├── id_produit (FK → produits)
├── id_historique (FK → historiques, nullable)
├── type_mouvement (ENTREE | SORTIE)
├── quantite, date_mouvement, description
```

---

## 8_ Données de test

Le seeder génère automatiquement :

| Entité | Nombre | Détail |
|--------|--------|--------|
| Utilisateurs | 1 | Admin (admin@gmail.ma) |
| Catégories | 8 | Lubrifiants, Filtres, Freinage, Électricité... |
| Produits | 27 | Avec stocks réalistes et alertes |
| Clients | 12 | Clients marocains fictifs réalistes |
| Services | 25+ | Répartis sur 4 mois avec calculs cohérents |
| Mouvements stock | 80+ | Entrées initiales + sorties services + réappros |

### Produits en alerte (pour tester les alertes)

Après le seed, ces produits déclenchent des alertes :
- **Graisse Multi-usages** — Stock faible (< seuil 5)
- **Liquide de Frein DOT4** — Stock faible (< seuil 6)
- **Kit Distribution Complet** — Rupture de stock (0)

---

## 9_ Modules détaillés

### Logique de décrémentation automatique du stock

Lors de l'enregistrement d'un service (POST /historique) :

```
1. Vérifier stock disponible pour chaque produit
2. Créer l'enregistrement Historique
3. Pour chaque produit :
   a. Créer DetailHistorique (quantite, prix_unitaire, prix_total)
   b. Décrémenter quantite_stock dans la table produits
   c. Créer mouvement GestionStock (type: SORTIE, lié à l'historique)
4. Calculer et sauvegarder montant_total
```

### Logique de restauration du stock (suppression service)

```
1. Pour chaque détail du service :
   a. Incrémenter quantite_stock du produit
   b. Créer mouvement GestionStock (type: ENTREE, motif: annulation)
2. Supprimer le service (cascade sur detail_historiques)
```

---

## 10_ Routes disponibles

```
GET    /dashboard
GET    /clients
GET    /clients/create
POST   /clients
GET    /clients/{id}
GET    /clients/{id}/edit
PUT    /clients/{id}
DELETE /clients/{id}

GET    /categories
GET    /categories/create
POST   /categories
GET    /categories/{id}/edit
PUT    /categories/{id}
DELETE /categories/{id}

GET    /produits
GET    /produits/create
POST   /produits
GET    /produits/{id}
GET    /produits/{id}/edit
PUT    /produits/{id}
DELETE /produits/{id}
POST   /produits/{id}/ajouter-stock

GET    /historique
GET    /historique/create
POST   /historique
GET    /historique/{id}
GET    /historique/{id}/edit
PUT    /historique/{id}
DELETE /historique/{id}
GET    /historique/{id}/facture        ← PDF
GET    /historique-mensuel             ← Vue mensuelle
GET    /historique-mensuel?export_pdf  ← PDF mensuel

GET    /stock
GET    /stock/etat
POST   /stock/entree

GET    /login
POST   /login
POST   /logout
```

---

## 11_ Déploiement en production

```bash
# Optimisation Laravel pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Variables d'environnement production
APP_ENV=production
APP_DEBUG=false
```


## 12_ Captures d'écran

> Les captures d'écran sont disponibles après installation.

| Page | Description |
|------|-------------|
| `/login` | Page de connexion élégante |
| `/dashboard` | Vue d'ensemble statistiques |
| `/clients` | Liste clients avec recherche |
| `/historique/create` | Formulaire service dynamique (JS) |
| `/produits` | Catalogue avec alertes stock |
| `/stock/etat` | Inventaire temps réel |
| `/historique/{id}/facture` | Facture PDF professionnelle |

---

## 13_ Dépendances principales

```json
{
  "require": {
    "php": "^8.5",
    "laravel/framework": "^12.0",
    "barryvdh/laravel-dompdf": "^2.0",
    "laravel/sanctum": "^3.2"
  }
}
```

---

## 👨‍💻 Auteur

Développé avec [hamzaElAsly](https://github.com/hamzaElAsly) pour un projet de fin d'études (PFE) de niveau professionnel.

- **Stack** : Laravel 12 + MySQL + Bootstrap 5
- **Architecture** : MVC Clean
- **Niveau** : PFE / Projet professionnel

---

<p align="center"> **Charrak TECHNOLOGIE** — Gestion Commerciale Professionnelle<br>
  <em>Fait avec Laravel et Bootstrap 5</em>
</p>