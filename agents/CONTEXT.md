# Екосистема Concept 2 і роль concept-recipes

## Що таке Concept 2

Strict-typed PHP-стек для **PHP 8.4+**, PSR (HTTP, Container, Logging). **Не** монолітний framework і **не** CMS.

Чотири шари:

| Шар | Пакет | Роль |
|-----|-------|------|
| **Core** | `php-concept/core-2` | `App`, `HttpKernel`, `RouteStrategy`, базові argument resolvers |
| **Extensions** | `php-concept/extension-*` | Низькорівневі ServiceProvider / contracts (Http, Validation, DB, …) |
| **Bricks** | `php-concept/brick-*` | Fluent glue: `*Brick` + `*Builder` + `*Options` + `*StackProvider` |
| **Stack** | `php-concept/stack` ^2 | Thin assembler: `ConceptStack::create()` → `add(BrickInterface)` → `providers()` |

Додаток збирає Stack у glue (Bootstrap): `$stack->add(new HttpBrick())->builder()->…`. Bricks тягнуть потрібні `extension-*` транзитивно. Foundation (Config, PathManager) лишається окремими extensions без brick.

## Два репозиторії в цьому workflow

| Репо | Путь | Роль |
|------|------|------|
| **concept-docs-2** | `/var/www/concept/concept-docs-2` | Статична документація (Bootstrap 5, dark, highlight.js). UK в `uk/`. |
| **concept-recipes** | `/var/www/concept/concept-recipes` | Runnable glue + Docker; код для кожного кроку рецепта |

**Синхронізація:** крок «готовий» = тег у recipes + повна HTML-сторінка в docs + `gitCheckout` у `nav-uk.js`.

## Layout на хосту (maintainer)

```
/var/www/concept/
  concept-core-2/          ← php-concept/core-2
  concept-stack/           ← php-concept/stack (thin ^2)
  concept-bricks/          ← brick-* monorepo (packages/*)
  concept-extensions/      ← extension-* monorepo (packages/*)
  concept-docs-2/          ← документація
  concept-recipes/         ← цей репо (glue + agents/)
  concept-skeleton-2/      ← еталон production glue API
```

Maintainer редагує Core/Stack/Bricks/Extensions на хосту; у dev-контейнері recipes — **symlinks** через `composer-dev.json` (`packages/*` path repos).

## concept-recipes — що це

Мінімальний glue-додаток для документації:

- Не skeleton і не production app
- Кожен крок додає один brick у `ApplicationStackBootstrap`
- Спочатку **literal paths** у bootstrap (без Config/PathManager); крок 11 переводить на Foundation + glue
- Крок 10 — опційний Casting (DTO / typed routes)
- Еталон API — `concept-skeleton-2` / `concept-skeleton-dev-2`

**Базові 5 файлів (крок 1):**

1. `public/index.php` — autoload → bootstrap → `App::handle()` → SapiEmitter
2. `bootstrap/app.php` — `App::create()` + `ApplicationStackBootstrap::boot()`
3. `routes/web.php` — маршрути (`$router` у scope від HttpKernel)
4. `src/App/Bootstrap/ApplicationStackBootstrap.php` — Stack recipe
5. Controller — handler маршруту

**Namespace:** `Concept\App\…` (PSR-4 `Concept\` → `src/`).

## Документація — структура nav (uk)

Файл: `/var/www/concept/concept-docs-2/assets/js/nav-uk.js`

1. **Пролог** — Вступ, Глосарій
2. **Створення додатку** — overview + 12 кроків (основний learning path)
3. **Stack** — огляд, bricks, lifecycle
4. **Extensions** — по одному пакету (під капотом bricks)
5. **Core** — container, routing, middleware, …
6. **Довідник** — installation, config map, migration 1.x

## Життєвий цикл HTTP-запиту (крок 1)

```
public/index.php
  → bootstrap/app.php
    → ApplicationStackBootstrap::boot()
      → ConceptStack::add(new HttpBrick())->builder() → providers()
  → App::handle()
    → Router::dispatch(request)
      → RouteStrategy → IndexController::index()
  → SapiEmitter
```

## Важливі правила для агента

- **Публічна docs** — без внутрішніх нотаток про треки/відгалуження (це в `agents/`).
- **Users** працюють з `composer.json` + Packagist + `docker-compose.yml.example`.
- **Maintainers** — `composer-dev.json` + symlinks + `docker-compose.dev.yml.example` (mount core, stack, bricks, extensions).
- Заголовки сторінок docs синхронізуються з nav через `syncPageTitle()` в `docs.js`; **label у nav = source of truth**.
- Не комітити без явного запиту (user rule).
- Не повертати `addHttp()` / `addView()` на `StackBuilder` — thin stack лише `add(BrickInterface)`.

## Детальні інструкції Core

`/var/www/concept/concept-core-2/AGENTS.md` — ядро, resolvers, без ConfigInterface.
