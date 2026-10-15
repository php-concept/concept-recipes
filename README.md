# concept-recipes

Runnable companion apps for **Concept 2** documentation ([concept-docs-2](https://github.com/php-concept/concept-docs-2)).

Each recipe step is a small, working contact application that matches one chapter of the “Creating an application” guide. Docs explain *why* and *how*; this repo is the code you can check out and run.

Linear history on **`main`**: one commit per step, tags `step-*`. No `track/web` / `track/api` branches (API and web merge at step 6 in the linear story).

## Step tags

```bash
git clone <this-repo> concept-recipes && cd concept-recipes
git checkout step-01-http    # or any tag below
rm -rf storage/cache storage/sessions storage/logs
composer install
```

| Step | Tag | What you get |
|------|-----|--------------|
| 1. First HTTP request | `step-01-http` | `HttpBrick` only |
| 2. HTML pages | `step-02-view` | `ViewBrick` + Twig |
| 3. Validation | `step-03-validation` | `ValidationBrick` + FormRequest |
| 4. JSON API | `step-04-json-api` | `routes/api.php`, JSON middleware |
| 5. Session & CSRF | `step-05-session` | `SessionBrick`, `withCsrf()` |
| 6. Database | `step-06-database` | `DatabaseBrick`, web + api routes |
| 7. Error handling | `step-07-error-handling` | `ErrorHandlingBrick` |
| 8. Logging & masking | `step-08-logging` | `LoggingBrick` + `MaskingBrick` |
| 9. Diagnostic logs | `step-09-diagnostic-logs` | validation / query log files |
| 10. Casting & DTO | `step-10-casting` | `CastingBrick`, FormRequest → DTO |
| 11. Config | `step-11-config` | `config/`, `.env`, `AppConfig` |
| 12. PathManager | `step-12-path-manager` | `FoundationBootstrap`, named paths |

**Tip:** clear `storage/` after switching tags so Twig/Casting cache and sessions from the previous step do not leak into the browser.

### Logical fork (docs only)

In git history, step 4 is the API line (no View/Session); step 5 returns the web line. Both meet again at step 6. Use linear tags above — no separate branch checkout.

## Quick start (readers)

Packagist packages only — no sibling repos:

```bash
git checkout step-12-path-manager   # latest recipe step
cp .env.example .env                # required from step 11 onward
composer install
cp docker-compose.yml.example docker-compose.yml
docker compose up -d --build
docker compose exec app php bin/console.php db:migrate
curl -s http://localhost:8089/
curl -s http://localhost:8089/api/stats
```

- App: [http://localhost:8089](http://localhost:8089)  
- MySQL on host port **3366** (service `db` inside Compose)

From **step 11**, configuration is loaded from `config/*.php` and a **`.env` file on disk**. Do not put `APP_DEBUG` / `DB_*` only in `docker-compose.yml` `environment:` — Config reads the file, not container env vars. Use `DB_HOST=db` in `.env` when the app runs inside Compose (service name), or `concept_recipes_db` / `127.0.0.1` for your local setup.

Earlier steps (1–10) use `getenv()` and literal paths in bootstrap; no `.env` required until step 11.

## Maintainer DX

Local development against sibling trees under `/var/www/` (`concept-core-2`, `concept-stack`, `concept-bricks`, `concept-extensions`):

```bash
cp docker-compose.dev.yml.example docker-compose.yml
cp composer-dev.json.example composer-dev.json
docker compose up -d --build
docker compose exec app composer install   # uses COMPOSER=composer-dev.json from compose
```

`docker-compose.yml`, `composer-dev.json`, and `composer-dev.lock` are gitignored when copied from examples.

**Important:** run Composer **inside the container** (`docker compose exec app composer install|update`). The compose file sets `COMPOSER=composer-dev.json` and mounts sibling repos so `vendor/` uses symlinks to local packages. Running `composer update` on the host without `COMPOSER=composer-dev.json` replaces symlinks with Packagist copies and can break the app against local stack/bricks.

Stack glue API (tip / step 12):

```php
$stack->add(new HttpBrick())
    ->builder()
    ->setRoutes([...]);
```

Maintainer notes: [`agents/`](agents/) (Ukrainian, not published to concept-docs-2).

## Documentation map

| Step | Guide (UK) |
|------|------------|
| Overview | [recipes-overview](https://github.com/php-concept/concept-docs-2/blob/main/uk/recipes/recipes-overview.html) |
| 1–12 | `uk/recipes/recipe-step-*.html` in [concept-docs-2](https://github.com/php-concept/concept-docs-2) |

After step 12, continue with Stack and Bricks / Extensions sections in the same docs site.
