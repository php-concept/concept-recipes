# Кроки рецепта «Створення додатку»

Документація: `/var/www/concept-docs-2/uk/`. Nav labels і `gitCheckout` — `/var/www/concept-docs-2/assets/js/nav-uk.js`.

## Огляд

| # | Nav label | HTML slug | Stack / зміст | Статус коду | Статус docs |
|---|-----------|-----------|---------------|-------------|-------------|
| — | Створення додатку | `recipes-overview` | overview | — | готово |
| 1 | 1. Перший HTTP-запит | `recipe-step-01-http` | `HttpBrick` | **готово** (tip: brick API) | потребує sync |
| 2 | 2. HTML-сторінки | `recipe-step-02-view` | `ViewBrick` + Twig | **готово** | потребує sync |
| 3 | 3. Валідація | `recipe-step-03-validation` | `ValidationBrick` + FormRequest | **готово** | потребує sync |
| 4 | 4. JSON API | `recipe-step-04-json-api` | JSON middleware, `routes/api.php` | **готово** | потребує sync |
| 5 | 5. Session і CSRF | `recipe-step-05-session` | `SessionBrick`, `withCsrf()` | **готово** | потребує sync |
| 6 | 6. База даних | `recipe-step-06-database` | `DatabaseBrick`, merge web+api | **готово** | потребує sync |
| 7 | 7. Обробка помилок | `recipe-step-07-error-handling` | `ErrorHandlingBrick` | **готово** | потребує sync |
| 8 | 8. Логування і маскування | `recipe-step-08-logging` | `LoggingBrick` + `MaskingBrick` | **готово** | потребує sync |
| 9 | 9. Діагностичні логи | `recipe-step-09-diagnostic-logs` | `withLogging` / `withQueryLogging` | **готово** | потребує sync |
| 10 | 10. Casting і DTO | `recipe-step-10-casting` | `CastingBrick`, `toDto()` | **готово** | потребує sync |
| 11 | 11. Config | `recipe-step-11-config` | `ConfigBootstrap` → step 12: `FoundationBootstrap` | **готово** | потребує sync |
| 12 | 12. PathManager | `recipe-step-12-path-manager` | `FoundationBootstrap`, `PathName` | **готово** | потребує sync |

## Принципи (публічні, в recipes-overview)

- Один крок = один новий brick у Stack
- Кожен крок будує на попередньому
- Stack не читає config — explicit values у Bootstrap через `->builder()`
- Middleware lists — у `routes/*.php`, не в Stack
- Спочатку literal paths; Config/PathManager — крок 11
- Entry API: `$stack->add(new XBrick())->builder()->…` (не `addHttp()`)

## Крок 1 — деталі (реалізовано)

### ApplicationStackBootstrap

```php
$stack = ConceptStack::create();
$stack->add(new HttpBrick())
    ->builder()
    ->setRoutes([$rootPath . 'routes/web.php']);
foreach ($stack->providers() as $provider) {
    $container->addServiceProvider($provider);
}
```

### routes/web.php

```php
$router->get('/', [IndexController::class, 'index'])->setName('home');
```

### IndexController

`ResponseFactory` з Http extension → body `Hello, world!`.

### composer.json (users)

```json
"require": {
    "php": "^8.4",
    "php-concept/core-2": "^1.0",
    "php-concept/stack": "^2.0",
    "php-concept/brick-http": "^1.0"
}
```

+ `autoload`: `Concept\\` → `src/`
+ `brick-http` підтягує `extension-http` транзитивно

### Перевірка

```bash
curl -s http://localhost:8089/
# Hello, world!
```

## Крок 2 — деталі (реалізовано)

- `add(new ViewBrick())->builder()->withTwig()->setViewsPath(...)->setCacheDir(...)`
- `resources/views/index.twig` + `public/css|js`
- Controller → `ViewResponseFactoryInterface::create('index')`
- Bootstrap/Icons у `composer.json`; `public/vendor/` і `storage/` — gitignore
- Tag: `step-02-view` / `web/step-02-view` на `track/web`

```php
$stack->add(new ViewBrick())
    ->builder()
    ->withTwig()
    ->setViewsPath($rootPath . 'resources/views')
    ->setCacheDir($rootPath . 'storage/cache/views');
```

### Перевірка

```bash
curl -sI http://localhost:8089/ | grep -i content-type
# text/html
```

## Крок 3 — деталі (реалізовано)

- `addValidation()` (FormRequest factory); `withFormRequests()` лише для type-hint у action
- `ContactFormRequest`, `ContactController`, `GET|POST /contact`
- `resources/views/contact.twig` + лінк з головної
- `public/.htaccess` для Apache rewrite
- Валідація через `FormRequestFactory` (re-render на 422; type-hint — після ErrorHandling)
- Tag: `step-03-validation` на `track/web`

## Крок 4 — план (API track)

- `routes/api.php`
- JSON middleware (`extension-json`)
- API controllers, без View

## Крок 5 — деталі (реалізовано)

- `addSession()->setHandlerFactory(...)->withCsrf()`
- `SessionKey` (`URL_CURRENT`, `URL_PREVIOUS`) + `StorePreviousUrlMiddleware`
- `VerifyCsrfTokenMiddleware` + `HandleCsrfExceptionMiddleware` + `StorePreviousUrlMiddleware` у `routes/web.php`
- `ContactController` → token + `flashes` у view; `contact.twig` показує `flashes.error`
- Tag: `step-05-session`

### Перевірка

```bash
mkdir -p storage/sessions storage/cache/views
curl -s -c /tmp/cookies.txt http://localhost:8089/contact
# POST без _csrf_token → 302 на /contact + flash «CSRF token mismatch»
# POST з token + valid fields → 200
# При зміні contact.twig: rm -rf storage/cache/views (Twig compile cache)
```

**Пізніше (крок 7):** `ShareViewDataMiddleware` → `VIEW_PAYLOAD` для csrf/flashes/errors/old.

## Крок 6 — деталі (реалізовано, merge point)

- Merge `track/api` → `track/web`: обидва `routes/web.php` + `routes/api.php`
- `addDatabase()->setConnection([…])->setMigrations([…])`
- `addConsole()->setCommands([DbMigrateCommand, DbRollbackCommand, DbMigrationListCommand, DbSeedCommand, DbSeederListCommand])`
- `database/migrations/V2026_08_07_120000_CreateContactSubmissionsTable.php`
- `database/seeders/ContactSubmissionsSeeder.php` + `setSeeders([...])`
- Autoload `Database\\Seeders\\` → `database/seeders/`
- `ContactController` + `ApiController` → `DatabaseInterface`; web і API `POST` зберігають у БД; `GET /api/stats`
- MySQL у `docker-compose.yml.example` / `docker-compose.dev.yml.example` (port 3366)
- `bin/console.php` — `db:migrate`
- CSRF middleware — група web-маршрутів (не global router), щоб `/api/*` не ловив CSRF
- Tag: `step-06-database`

### Перевірка

```bash
docker compose exec app php bin/console.php db:migrate
curl -s http://localhost:8089/api/stats
curl -s -X POST http://localhost:8089/api/contact \
  -H 'Content-Type: application/json' \
  -d '{"name":"Anna","email":"anna@example.com","message":"Hello there friend"}'
curl -s http://localhost:8089/api/stats   # total: 1
```

## Крок 7 — деталі (реалізовано)

- `addErrorHandling()->setDebug(APP_DEBUG, PrettyPageHandler)->setLogReporting(logger: false, phpErrorLog: true)->withViewRenderer(fallback)`
- **Без** `addLogging()` — Monolog на кроці 8
- `SessionKey` + `VALIDATION_ERRORS` / `VALIDATION_DATA` (URL-ключі з кроку 5)
- `withFormRequests()` — type-hint `ContactFormRequest` (web + API)
- `HandleValidationExceptionMiddleware` + `ShareViewDataMiddleware` у web group
- `HandleJsonValidationExceptionMiddleware` у `routes/api.php`; `ApiController::contact(ContactFormRequest)`
- `BoomController` `/boom`; `resources/views/errors/fallback/{404,500}.php`
- `APP_DEBUG` у docker-compose examples
- Tag: `step-07-error-handling`

### Перевірка

```bash
# invalid POST → 302 + flash errors
curl -s http://localhost:8089/boom          # Whoops якщо APP_DEBUG=1
# APP_DEBUG=0 → fallback 500.php
```

## Крок 8 — деталі (реалізовано)

- `addMasking()->setPatterns([...])->setKeyPatterns([email, password, token, …])`
- `addLogging()->setLevel('INFO')->setChannel('recipes')->toRotatingFile(...)->withMasking()`
- `setLogReporting(logger: true, phpErrorLog: true)` на ErrorHandling
- `LoggerInterface` у `ContactController::submit` → `info` з `email` у context (маскиться до `***`)
- Tag: `step-08-logging`

### Перевірка

```bash
# після валідного POST /contact
tail -n 5 storage/logs/app-*.log
# {"email":"***"}
curl -s -o /dev/null http://localhost:8089/boom
tail -n 20 storage/logs/app-*.log
```

## Крок 9 — деталі (реалізовано)

- `addValidation()->withLogging(storage/logs/validation.log)->withMasking()`
- `addDatabase()->…->withQueryLogging(storage/logs/query.log)->withMasking()`
- Окремі файли ≠ `app.log` / `addLogging()`
- Tag: `step-09-diagnostic-logs`

### Перевірка

```bash
# invalid POST → validation-*.log
# GET /api/stats або valid contact → query-*.log
ls -la storage/logs/
```

## Крок 10 — Casting і DTO

- `add(new CastingBrick())->builder()->setCacheDir(...)->setDebug(...)`
- `HttpBrick` + `withTypedRouteParameters()` — `{id}` → `int $id` (`GET /contact/{id}`)
- `ContactDto` + `ContactFormRequest::$dtoClass` + `$form->toDto()`
- `contact-show.twig` + `ContactController::showById`
- Controllers: `insert($dto->toArray())`
- Tag: `step-10-casting`

### Перевірка

```bash
curl -sS -X POST http://127.0.0.1:8089/api/contact \
  -H 'Content-Type: application/json' \
  -d '{"name":"Ada","email":"ada@example.com","message":"Hello casting world"}'
```

## Крок 11 — Config (реалізовано)

- `config/` — `app`, `db`, `log`, `validator`, `masking` (5 файлів)
- `ConfigBootstrap` + `ConfigServiceProvider`; `.env.example`
- `composer.json` → `php-concept/extension-config`
- `ConfigKey`, `AppConfig` (composite: `dbConnection`, masking, seeders; scalars → `ConfigInterface`)
- `ApplicationRuntimeBootstrap` — timezone
- Bootstrap: значення з config; шляхи ще `$rootPath` (крок 12 — PathManager)
- Tag: `step-11-config`

### Перевірка

```bash
cp .env.example .env
git checkout step-11-config
# contact + api/stats як на step-10; зміни config/app.php debug або DB_HOST у .env
```

## Крок 12 — PathManager (реалізовано)

- `bootstrap/path-map.php`, `PathName` — іменовані шляхи (мінімальний набір для contact-app)
- `FoundationBootstrap` замість `ConfigBootstrap` (PathManager + Config)
- `ApplicationStackBootstrap` без `$rootPath` concat — `$pathManager->get(...)`
- `composer.json` → `php-concept/extension-path-manager`
- Tag: `step-12-path-manager`

### Перевірка

```bash
cp .env.example .env
git checkout step-12-path-manager
# contact + api/stats як на step-11; шляхи тільки через PathManager
```

## composer-dev.json (maintainer)

Path repositories з symlink до:

- `../concept-core-2`
- `../concept-stack`
- `../concept-extensions/*` (всі extension dirs для майбутніх кроків)

Шаблон: `composer-dev.json.example` (в репо). Локальний `composer-dev.json` gitignored.

`require` на крок 1: stack `@dev`, extension-http `^1.0`.

## Що додавати на кожному кроку в docs

Патерн сторінки (як `recipe-step-01-http.html`):

1. Вступний абзац
2. «Що входить у збірку» (опційно)
3. Файли по порядку читання запиту (1…n)
4. «Запуск і перевірка» — users flow (`composer install`, docker example)
5. Maintainer note (маленький muted) — dev templates, **не** треки в основному тексті
6. Ланцюжок запиту (text diagram)
7. Alert з посиланням на наступний крок (**без** `<h3>` в alert — не лізе в TOC)
8. `data-page` на `<body>` = slug
9. Плашка `git checkout` — автоматично з `gitCheckout` у nav

## nextPrev (nav-uk.js) — лінійний порядок у docs

```
overview → 1 → … → 10 (casting) → 11 (config) → 12 (path-manager) → stack-overview → …
```

Це **навігація для читача**, не git graph. Відгалуження git — див. GIT-BRANCHES-TAGS.md.
