# Roadmap: concept-recipes + concept-docs-2

## Текущий стан (snapshot)

| Компонент | Стан |
|-----------|------|
| concept-recipes tip (step 12 glue) | ✓ `add(new *Brick())->builder()` + `stack ^2` + `brick-*` |
| concept-recipes git step-* tags | ✓ rewrite: 1 commit = 1 крок, brick API |
| concept-recipes Docker | ✓ users + dev (+ `concept-bricks` mount) |
| concept-docs рецепти | ⚠ лістинги зі старим `addHttp()` — sync з tags |
| Stack / Extensions / Core sections | частково; bricks naming у docs — sync |
| `composer.lock` | ✓ users (`stack` v2 + bricks) |

---

## Етап M0 — foundation (найближчий)

### concept-recipes

- [ ] Initial commit (glue, docker, composer.json, lock, examples, `agents/`)
- [ ] Гілка `track/web` від initial
- [ ] Тег `step-01-http`
- [ ] (опційно) `track/api` від same commit для майбутнього fork
- [ ] `bin/boot-smoke.php` + script у `composer.json`
- [ ] Зняти `/README.md` з gitignore або додати committed README для GitHub

### concept-docs-2

- [x] Вступ, overview, крок 1
- [ ] Перевірити посилання на repo URL після publish

---

## Етап M1 — кроки 2–3 (web + спільна валідація)

### Recipes

- [x] `addView()` + Twig, views, controller
- [x] Commit на `track/web`, тег `web/step-02-view`
- [x] Validation + FormRequest
- [x] Тег `step-03-validation` на web; cherry-pick на `track/api` — пізніше

### Docs

- [x] Повна сторінка `recipe-step-view.html`
- [x] Повна сторінка `recipe-step-validation.html`
- [ ] Оновити `gitCheckout` у nav якщо теги з префіксом

---

## Етап M2 — відгалуження (крок 4 API, крок 5 Web)

### Recipes

- [x] `track/api`: JSON API, `routes/api.php`, extension-json → `api/step-04-json-api` / `step-04-json-api`
- [x] `track/web`: Session + CSRF → `web/step-05-session` / `step-05-session`

### Docs

- [x] `recipe-step-json-api.html` (API surface)
- [x] `recipe-step-session.html` (web forms)

**Git:** docs next/prev лінійний; читач API використовує sidebar або overview.

---

## Етап M3 — злиття + інфра (крок 6–7)

### Recipes

- [x] MySQL у docker-compose examples
- [x] `addDatabase()`, migrations, seeders, rollback, merge api → web
- [x] Тег `step-06-database`
- [ ] Cherry-pick / sync на `track/api` для спільного step-06
- [x] `addErrorHandling()` + validation/share middleware (без Logging)
- [x] Тег `step-07-error-handling`
- [x] Крок 8: `addLogging()` + `addMasking()`
- [x] Тег `step-08-logging`
- [x] Крок 9: діагностичні логи (`withLogging` / `withQueryLogging`)
- [x] Тег `step-09-diagnostic-logs`
- [x] Крок 10: Casting + DTO (`addCasting`, `toDto`, typed routes)
- [x] Тег `step-10-casting`
- [ ] Крок 11: Config + PathManager

### Docs

- [x] `recipe-step-database.html`, `stack-brick-database.html`
- [x] `recipe-step-error-handling.html`, `stack-brick-error-handling.html`
- [x] `recipe-step-logging.html` + `recipe-step-diagnostic-logs.html` (Why first)
- [x] `recipe-step-casting.html` + `stack-brick-casting.html`
- [x] stubs Why-intro: config
- [ ] Повні docs кроку 11 (Config)
- [ ] Наповнити `request-lifecycle.html`

---

## Етап M4 — Config (крок 11) ✓

### Recipes

- [x] `extension-config`, `config/`, `ConfigBootstrap`, `AppConfig`
- [x] `.env.example` (`DB_*`, `APP_ENV=dev`)
- [x] Тег `step-11-config`

### Docs

- [x] `recipe-step-11-config.html`
- [ ] stub `recipe-step-12-path-manager.html` (PathManager — крок 12)
- [ ] Початок Stack section (`stack-overview`, `stack-bricks`)

## Етап M4b — PathManager (крок 12) ✓

### Recipes

- [x] `extension-path-manager`, `path-map.php`, `PathName`, `FoundationBootstrap`
- [x] Bootstrap без `$rootPath` concat
- [x] Тег `step-12-path-manager`

### Docs

- [x] `recipe-step-12-path-manager.html`
- [ ] Початок Stack section (`stack-overview`, `stack-bricks`)

---

## Етап M5 — Stack + Extensions docs

Порядок наповнення:

1. Stack overview → bricks → lifecycle (вже частково)
2. Extensions по порядку кроків: Http → Validation → Form Request → … → Config
3. Кожна extension page зв’язана з відповідним кроком рецепта де можливо

---

## Етап M6 — Core + Довідник

- Core overview → container → providers → routing → middleware → interceptors → resolvers → controllers
- Installation, config-reference, upgrade-from-1x
- Glossary (stub → content)

---

## Критерій «крок готовий»

1. Код працює: `curl` / boot-smoke
2. `composer.json` + lock оновлені (users)
3. Docker examples валідні (db на крок 6+)
4. Git тег на правильній гілці
5. HTML сторінка в docs (не stub)
6. `nav-uk.js`: label + gitCheckout
7. Плашка checkout на сторінці
8. Users quick start в статті

---

## Ризики / незакриті

- `README.md` gitignored — GitHub без README поки не змінимо gitignore
- Nav `gitCheckout` vs внутрішні теги `web/` `api/` — синхронізувати при першому branch-кроку
- `composer-dev.json` не в репо (тільки example) — новий maintainer копіює example
- Документація EN (`nav-en.js`) — стара 1.x, не синхронізована з 2.x uk

---

## Хронологія (орієнтовно)

```
M0 ──► M1 (2,3) ──► M2 (fork 4,5) ──► M3 (6,7+db) ──► M4 (8) ──► M5 ──► M6
         web+common      api/web         merge           glue      ext docs  core
```

Деталі гілок: [GIT-BRANCHES-TAGS.md](./GIT-BRANCHES-TAGS.md)
