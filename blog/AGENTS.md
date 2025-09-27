# Repository Guidelines

## Project Structure & Module Organization
- `app/`: Application code (Models, Controllers, Middleware).
- `resources/`: Blade views, JS (`resources/js`), CSS (`resources/css`).
- `routes/`: HTTP and console routes (`web.php`, `console.php`).
- `database/`: Migrations, seeders, factories.
- `tests/`: Pest/PHUnit tests (`tests/Feature`, `tests/Unit`).
- `public/`: Public entry (`index.php`), assets.
- `config/`: Framework and app configuration.

## Build, Test, and Development Commands
- Install deps: `composer install` and `npm install`.
- Local dev (PHP + queue + Vite): `composer dev`.
- Serve manually: `php artisan serve` and `npm run dev`.
- Build assets: `npm run build` (Vite).
- Run tests: `composer test` or `php artisan test`.
- Migrate/seed: `php artisan migrate --seed`.
- Docker workflow: `./docker-setup.sh` (first run), `./docker-start.sh`, `./docker-stop.sh`, `./docker-artisan.sh migrate`, `./docker-npm.sh run dev`.

## Coding Style & Naming Conventions
- Indentation: 4 spaces (see `.editorconfig`); YAML: 2 spaces.
- PHP: Follow PSR-12 and Laravel conventions; run `./vendor/bin/pint` before committing.
- Tests: Suffix files with `Test.php` (e.g., `UserServiceTest.php`).
- Controllers: `PascalCaseController` (e.g., `ClienteController`); Eloquent models in `App\Models` use singular `PascalCase`.
- Branches: `feature/short-desc`, `fix/issue-123`.

## Testing Guidelines
- Framework: Pest on top of PHPUnit.
- Locations: `tests/Feature` for HTTP/flow; `tests/Unit` for small units.
- Run: `composer test` locally or `./docker-artisan.sh test` in Docker.
- Keep tests fast and deterministic; use factories for data.

## Commit & Pull Request Guidelines
- Commits: Prefer Conventional Commits (`feat:`, `fix:`, `chore:`). Write in imperative mood and keep concise.
- PRs: Include purpose, linked issues, steps to verify, and screenshots for UI changes (`resources/views`).
- Pre-PR checklist: tests pass, `pint` run cleanly, assets build (`npm run build`), and migrations included when schema changes.

## Security & Configuration Tips
- Never commit secrets; use `.env`/`.env.docker`. Ensure `APP_KEY` is set.
- For Docker, use provided scripts and fix permissions if needed:
  `docker compose exec app chown -R www-data:www-data storage bootstrap/cache`.
- Queue workers run during `composer dev`; ensure long-running tasks are idempotent.

