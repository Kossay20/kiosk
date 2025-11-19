# Repository Guidelines

## Project Structure & Module Organization
This Laravel 10 codebase keeps application logic in `app/`, with HTTP controllers under `app/Http/Controllers`, models in `app/Models`, and Blade components in `app/View`. Frontend assets (Tailwind, Alpine, Vite entry points) live in `resources/js` and `resources/css`, while Blade templates sit in `resources/views`. Routes are split between `routes/web.php` for UI flows and `routes/api.php` for JSON endpoints. Database migrations, factories, and seeders stay in `database/`, and compiled assets are emitted to `public/build`. Automated tests reside in `tests/Feature` and `tests/Unit`.

## Build, Test, and Development Commands
Install backend dependencies with `composer install` and frontend tooling via `npm install`. Launch the application locally using `php artisan serve` and watch assets with `npm run dev`. Run `npm run build` before packaging releases to generate production CSS/JS. Apply schema changes through `php artisan migrate` (`--seed` when relying on seeded data). Refresh cached state after config changes with `php artisan config:clear` and `php artisan route:clear`.

## Coding Style & Naming Conventions
Follow PSR-12 with 4-space indentation for PHP and 2 spaces for Blade/JS. Use StudlyCase for classes and migrations (`CreateMenuTable`), camelCase for methods and variables, and snake_case for database columns. Run `./vendor/bin/pint` to auto-format PHP before committing. Keep Blade filenames kebab-case (`menu/index.blade.php`) and Vite modules lowercase-kebab for predictable import paths (`resources/js/components/menu-card.js`).

## Testing Guidelines
Use PHPUnit for backend coverage, grouping end-to-end scenarios in `tests/Feature` and isolated logic in `tests/Unit`. Execute the suite with `php artisan test`; add `--filter Feature/MenuManagementTest` when iterating. Pair new features with factories in `database/factories` and seeders in `database/seeders` to keep tests deterministic. Name methods descriptively, e.g., `test_guest_cannot_access_orders`, and extend the base `Tests/TestCase`.

## Commit & Pull Request Guidelines
Write imperative commit subjects under 72 characters (`Add cashier dashboard metrics`) and expand details in the body if needed. Scope each commit to one logical change and reference issue IDs in the footer (`Refs #123`). Pull requests should note behaviour changes, database impacts, screenshots for UI, and manual test steps. Request at least one peer review and wait for green CI before merging.

## Environment & Configuration Tips
Copy `.env.example` to `.env`, run `php artisan key:generate`, then configure local DB credentials. Default queue/mail/storage drivers are set to `sync` and `log`; document overrides when touching `config/*.php`. Never commit `.env`, `storage/`, or asset build outputs. For third-party integrations, store credentials via `.env` keys and provide sensible fallbacks in config files.
