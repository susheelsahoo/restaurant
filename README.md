# Tifliso (restaurant)

This repository contains the Tifliso restaurant website built with Laravel (v11.x). It includes a frontend (Blade + Bootstrap + custom CSS) and admin utilities (DataTables, Livewire, etc.).

## Contents

- Application code: `app/`
- Routes: `routes/`
- Views (frontend): `resources/views/`
- Assets: `public/assets/` and `resources/css` / `resources/js`
- Database migrations & seeders: `database/migrations`, `database/seeders`

## Prerequisites

- PHP 8.0+ (this project currently targets PHP ^8.0, but Composer may require ^8.2 depending on packages)
- Composer
- MySQL / MariaDB (or other supported DB)
- Node.js and npm (for building frontend assets)
- XAMPP / local server for development (optional)

## Quick setup (local)

1. Clone the repo

```bash
git clone <repo-url> tifliszo
cd tifliszo
```

2. Install PHP dependencies

```bash
composer install
```

3. Copy `.env` and generate app key

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure `.env`

- Set `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` for your database.
- Configure mail settings if you plan to send emails.

5. Run migrations & seeders

```bash
php artisan migrate --seed
```

6. Install frontend dependencies & build assets

```bash
npm install
npm run build   # or `npm run dev` for development with watcher
```

7. (Optional) Link storage

```bash
php artisan storage:link
```

8. Serve the app locally

```bash
php artisan serve
# or use XAMPP/Valet/Sail depending on your environment
```

## Common Artisan / Composer commands

- `composer install` — install PHP deps
- `composer update` — update deps (use with caution)
- `php artisan migrate` — run migrations
- `php artisan migrate:rollback` — rollback last batch
- `php artisan db:seed` — run seeders
- `php artisan route:list` — show routes
- `php artisan optimize:clear` — clear caches
- `php artisan vendor:publish --tag=livewire:assets --force` — publish Livewire assets

## Testing

- Run PHPUnit tests:

```bash
vendor/bin/phpunit
```

## Upgrading Laravel

- Upgrading major Laravel versions may require updating third-party packages. Test in a branch and use `composer update --with-all-dependencies` after adjusting `composer.json`.

## Notes & Troubleshooting

- If you hit dependency conflicts when running `composer update` (e.g., package X requires an older Laravel), either pin Laravel to the supported major version or wait for upstream package updates.
- If you see permission issues on Linux/macOS, ensure `storage/` and `bootstrap/cache` are writeable.
- If public assets are missing, run `php artisan vendor:publish` for packages that provide assets (Livewire, etc.).

## Admin scaffolding snippets

Examples used in the project or helpful for admin features:

```bash
php artisan make:model GalleryImage
php artisan make:controller Admin/GalleryController --resource
php artisan datatables:make PageDataTable
```

## Contributor guidelines

- Create feature branches named `feature/xxx` or `fix/xxx` and open pull requests to `master`.
- Run tests and linters before submitting PRs.

---

If you want, I can add a short `CONTRIBUTING.md`, Docker / Sail setup, or CI workflow next. Which would you prefer?
