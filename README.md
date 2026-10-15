# concept-recipes

Runnable companion apps for the **Concept 2** documentation ([concept-docs-2](https://github.com/php-concept/concept-docs-2)).

Each recipe step is a small, working application that matches one chapter of the “Creating an application” guide. Docs explain *why* and *how*; this repo is the code you can check out and run.

## Step tags

Check out a tag to get the app frozen at that documentation step:

```bash
git checkout step-01-http
```

| Step | Tag | Track | What you get |
|------|-----|-------|--------------|
| 1. First HTTP request | `step-01-http` | shared | `addHttp()` only |
| 2. HTML pages | `step-02-view` (`web/step-02-view`) | web | `addView()` + Twig |
| 3. Validation | `step-03-validation` | shared | `addValidation()` + FormRequest |
| 4. JSON API | `step-04-json-api` (`api/step-04-json-api`) | api | `routes/api.php`, JSON endpoints |
| 5. Session & CSRF | `step-05-session` (`web/step-05-session`) | web | `addSession()`, `withCsrf()` |
| 6. Database | `step-06-database` | shared | `addDatabase()`, web+api merge |
| 7. Error handling | `step-07-error-handling` | shared | `addErrorHandling()` |
| 8. Logging & masking | `step-08-logging` | shared | `addLogging()` + masking |
| 9. Diagnostic logs | `step-09-diagnostic-logs` | shared | request/query diagnostic logging |
| 10. Casting & DTO | `step-10-casting` | shared | `addCasting()`, FormRequest → DTO |
| 11. Config | `step-11-config` | shared | *not tagged yet* |

**Branches:** `track/web` (default line for steps 1→10 in the docs), `track/api` (JSON API fork at step 4).

Web path: `step-01-http` → `step-02-view` → `step-03-validation` → `step-05-session` → `step-06-database` → … → `step-10-casting`.

API path: `step-01-http` → `step-03-validation` → `step-04-json-api` (then shared tags from step 6).

## Quick start

Published Packagist packages — no sibling repos required:

```bash
git clone <this-repo> concept-recipes && cd concept-recipes
git checkout step-01-http   # or any step tag above
composer install
cp docker-compose.yml.example docker-compose.yml
docker compose up -d --build
curl -s http://localhost:8089/
```

## Maintainer DX

For local development against sibling Core / Stack / Extensions trees under `/var/www/`, use the `*.example` templates:

```bash
cp docker-compose.dev.yml.example docker-compose.yml
cp composer-dev.json.example composer-dev.json
COMPOSER=composer-dev.json composer install
docker compose up -d --build
```

`composer-dev.json`, `composer-dev.lock`, and `docker-compose.yml` are gitignored.
