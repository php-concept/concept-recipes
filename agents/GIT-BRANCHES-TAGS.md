# Git: гілки, теги, відгалуження

**Внутрішні нотатки.** Не додавати в публічну `concept-docs-2`.

## Модель

Гілка **`main`**: лінійна історія рецепту, **1 коміт = 1 крок** (кроки 1–12), далі — maintainer-коміти (pin `agents/`, scripts тощо).

| # | Коміт (EN) | Тег |
|---|------------|-----|
| 0 | `Initial commit` | — |
| 1 | `First HTTP request (step-01-http)` | `step-01-http` |
| 2 | `HTML pages and Twig (step-02-view)` | `step-02-view` |
| 3 | `Validation and FormRequest (step-03-validation)` | `step-03-validation` |
| 4 | `JSON API (step-04-json-api)` | `step-04-json-api` |
| 5 | `Session, CSRF, and flash messages (step-05-session)` | `step-05-session` |
| 6 | `Database, migrations, and MySQL (step-06-database)` | `step-06-database` |
| 7 | `Error handling with Whoops (step-07-error-handling)` | `step-07-error-handling` |
| 8 | `Logging with Monolog and masking (step-08-logging)` | `step-08-logging` |
| 9 | `Diagnostic validation and query logs (step-09-diagnostic-logs)` | `step-09-diagnostic-logs` |
| 10 | `Casting and DTO (step-10-casting)` | `step-10-casting` |
| 11 | `Config (step-11-config)` | `step-11-config` |
| 12 | `PathManager (step-12-path-manager)` | `step-12-path-manager` |

**Initial commit** — чистий скелетон: Docker, `composer.json` (core-2 + stack), `public/index.php`, порожній `ApplicationStackBootstrap`, `agents/`. Без routes, controllers, extensions.

**Мова:** код і commit messages — **англійською**; `agents/` — **українською**.

**`agents/` на всіх тегах `step-*`:** однаковий snapshot з tip `main` (або з `AGENTS_REF`). Кроки змінюють лише код додатку; внутрішні нотатки не «відкочуються» при `git checkout step-03-validation`. Редагуй `agents/` на `main`, потім `scripts/pin-agents.sh` (або повний rebuild).

## Схема відгалуження (логіка кроків)

```
                    ┌─ 2. View (web) ── 5. Session (web) ─┐
0. Skeleton ─ 1. HTTP ─ 3. Validation ─┤                              ├─ 6. DB ─ … ─ 10. Casting
                    └─ 4. JSON API (api) ─────────────────────┘
```

Крок 4 у лінійній історії — API-дерево (без View/Session). Крок 5 повертає web-гілку.

## Checkout для читача

```bash
git clone <repo> concept-recipes && cd concept-recipes
git checkout step-01-http    # або будь-який step-*
rm -rf storage/cache storage/sessions storage/logs
composer install
```

Або `./scripts/checkout-recipe-step.sh step-03-validation`.

## Workflow maintainer

1. `git checkout main`
2. Знайти коміт кроку: `git log --oneline` або `git checkout step-03-validation`
3. Внести зміни, `git commit --amend` (або `rebase -i` для перезапису кроку)
4. `git tag -f step-03-validation`
5. Rebase наступних кроків якщо змінився diff
6. Maintainer-коміти після останнього кроку — лише на tip `main` (не переписувати теги кроків без потреби)

## Що пушити

- Гілка **`main`** (default на GitHub)
- Теги **`step-*`** (12 штук, по одному на крок)

Історичні alias-теги `web/step-*`, `api/step-*` — видалені; використовуй лише `step-0N-…`.

## Що НЕ робити

- Не відновлювати `track/web` / `track/api` / `pre-release`
- Не force-push без явного запиту
- Не комітити `vendor/`, `storage/` runtime, maintainer symlinks
