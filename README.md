# 🛡️ ISPA Cyber Academy

**Plateforme ivoirienne d'e-learning en cybersécurité pour lycéens et étudiants.**

Projet de Fin d'Études (PFE) — ISPA Abidjan

---

## 📋 Technologies

| Composant | Technologie |
|-----------|------------|
| Backend | Laravel 11 / PHP 8.3 |
| Frontend | Blade + Tailwind CSS (CDN) |
| Base de données | MySQL 8 |
| Authentification | Laravel Sanctum |
| PDF | DomPDF |
| QR Code | Simple QRCode |

---

## 🚀 Installation (Laragon / Windows)

### Prérequis
- **PHP 8.3+**
- **Composer**
- **MySQL 8**
- **Laragon** (recommandé) ou XAMPP

### Étapes

```bash
# 1. Cloner ou extraire le projet
cd C:\laragon\www
# Extraire le zip ici → C:\laragon\www\ispa-cyber-academy

# 2. Installer les dépendances
cd ispa-cyber-academy
composer install

# 3. Configurer l'environnement
copy .env.example .env
php artisan key:generate

# 4. Créer la base de données
# Dans phpMyAdmin ou MySQL : CREATE DATABASE ispa_cyber_academy;
# Vérifier les infos dans .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 5. Lancer les migrations et le seeder
php artisan migrate --seed

# 6. Lancer le serveur
php artisan serve
```

Ouvrir **http://localhost:8000** dans le navigateur.

---

## 🔑 Comptes de démonstration

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| **Administrateur** | admin@ispa-cyber.ci | admin12345 |
| **Établissement** | lycee@ispa-cyber.ci | lycee12345 |
| **Étudiant** | jean@ispa-cyber.ci | etudiant123 |
| **Étudiante** | aicha@ispa-cyber.ci | etudiant123 |

---

## 📁 Structure du projet

```
ispa-cyber-academy/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/           → Inscription, Connexion, Déconnexion
│   │   │   ├── Admin/          → Dashboard admin, CRUD users/modules/établissements
│   │   │   ├── Student/        → Dashboard étudiant, cours, quiz, certificats
│   │   │   ├── Etablissement/  → Gestion classes/élèves par établissement
│   │   │   └── Forum/          → Forum communautaire
│   │   └── Middleware/
│   │       └── RoleMiddleware  → Contrôle d'accès par rôle
│   ├── Models/                 → 15 modèles Eloquent
│   └── Services/
│       └── GamificationService → Badges, points, certificats automatiques
├── database/
│   ├── migrations/             → 8 fichiers de migration (20+ tables)
│   └── seeders/                → Données de démonstration complètes
├── resources/views/
│   ├── layouts/app.blade.php   → Layout principal
│   ├── public/                 → Pages publiques (accueil, cours, contact)
│   ├── auth/                   → Login, Register
│   ├── dashboard/              → Tableau de bord étudiant
│   ├── courses/                → Navigation cours/leçons
│   ├── quiz/                   → Passage et résultats de quiz
│   ├── certificates/           → Affichage et PDF
│   ├── admin/                  → Administration
│   ├── etablissement/          → Espace établissement
│   └── forum/                  → Forum
└── routes/web.php              → Toutes les routes
```

---

## 🎮 Fonctionnalités

### 👤 Espace Étudiant
- Tableau de bord avec progression par module
- Navigation cours → leçons → quiz
- Timer de quiz avec correction immédiate
- Système de points et niveaux
- Badges automatiques (Premier Quiz, Expert Phishing, Champion…)
- Classement général
- Certificats PDF téléchargeables

### 🏫 Espace Établissement
- Gestion des classes
- Création de comptes élèves
- Suivi de progression individuel

### ⚙️ Administration
- CRUD utilisateurs avec filtres
- CRUD modules / leçons / quiz (constructeur de quiz dynamique)
- CRUD établissements
- Statistiques globales

### 🌐 Pages Publiques
- Page d'accueil avec présentation des menaces CI
- Catalogue des cours
- Vérification de certificats par numéro
- Page de contact

### 💬 Forum
- Création de sujets par module
- Réponses et discussions
- Sujets épinglés et verrouillables

---

## 🔐 Sécurité intégrée

- Hachage bcrypt des mots de passe
- Protection CSRF sur tous les formulaires
- Middleware de rôles (admin, etablissement, etudiant)
- Throttling des tentatives de connexion
- Journalisation des connexions (login_logs)
- Validation stricte de toutes les entrées
- Échappement XSS dans les vues Blade

---

## 📚 Contenus pédagogiques inclus (seed)

1. **Introduction à la cybersécurité** — Piliers CIA, menaces en Côte d'Ivoire
2. **Mots de passe sécurisés** — Création de mots de passe forts
3. **Détection du phishing** — Identification des arnaques SMS/email/réseaux sociaux

Chaque module contient des leçons avec quiz intégrés et corrections expliquées.

---

## 🛠️ Commandes utiles

```bash
# Réinitialiser la base de données
php artisan migrate:fresh --seed

# Vider les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Voir les routes
php artisan route:list
```

---

## 📧 Équipe PFE — ISPA Abidjan

| # | Rôle | Responsabilités |
|---|------|----------------|
| 1 | Chef de projet / Documentation | Coordination, cahier des charges |
| 2 | Frontend | Interfaces, UX/UI |
| 3 | Backend | API Laravel, logique métier |
| 4 | Base de données | Modèle relationnel |
| 5 | Contenus pédagogiques | Quiz, leçons, vidéos |
| 6 | Sécurité / Déploiement | Tests, Docker, hébergement |

---

**© 2025 ISPA Cyber Academy — Projet de Fin d'Études ISPA Abidjan**
