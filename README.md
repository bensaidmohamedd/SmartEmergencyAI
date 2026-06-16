# Smart Emergency AI — Niger

Plateforme de signalement d'urgences pour le Niger. Laravel 13, PHP 8.3+, Bootstrap 5.

## Prérequis

- PHP 8.3+ avec extensions : `pdo`, `mbstring`, `openssl`, `fileinfo`, `curl`
- [Composer](https://getcomposer.org/)

## Installation rapide (Windows)

```bat
setup.bat
```

Puis démarrer :

```bat
serve.bat
```

Ouvrir **http://127.0.0.1:8000**

### Installation manuelle

```bash
composer install
cp .env.example .env    # Windows: copy .env.example .env
php artisan app:install
php artisan serve:app
```

`app:install` crée les dossiers, la base SQLite, exécute migrations + seeders et le lien `storage`.

## Comptes de démo

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Citoyen | ben.said@email.ne | password |
| Admin | admin@smartemergency.ne | password |

## MySQL / XAMPP

Dans `.env`, remplacez la section SQLite par :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_emergency_ai
DB_USERNAME=root
DB_PASSWORD=
```

Puis : `php artisan app:install --fresh`

## Fonctionnalités

**Citoyens** — signalement GPS + IA, urgence rapide, annuaire secours (18/17/15), historique, notifications, attestation.

**Admin** — dashboard, centre opérationnel, vérification IA, services de secours, carte, export CSV, audit.

## Tests

```bash
php artisan test
```

## Numéros d'urgence Niger

- **18** — Pompiers
- **17** — Police / Gendarmerie
- **15** — SAMU / Ambulance

## Structure

```
app/Console/Commands/InstallApplicationCommand.php  # php artisan app:install
app/Services/EmergencyAnalysisService.php           # Moteur IA
routes/web.php
setup.bat / serve.bat                               # Windows
public/js/smart-emergency.js
```
