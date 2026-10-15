# Конвенції concept-docs-2

Путь: `/var/www/concept-docs-2/`

## Технології

- Статичний HTML, Bootstrap 5 dark (`data-bs-theme="dark"`)
- highlight.js + clipboard.js для код-блоків
- JS: `assets/js/nav-uk.js` (структура), `assets/js/docs.js` (sidebar, TOC, next/prev, плашки)

## Навігація (nav-uk.js)

`window.DOCS_NAV`:

- `sections[]` — групи з `title` + `items[]`
- `items[]`: `{ slug, label, gitCheckout? }`
- `nextPrev` — ланцюжок «Попередня / Наступна» внизу статті

### Заголовки сторінок

**Source of truth:** `label` у nav.

`docs.js` → `syncPageTitle()` підставляє текст у `<h1>` при load. HTML `<h1>` — fallback без JS.

**Важливо:** label на сторінці overview = «Створення додатку» (не «Огляд кроків»). Секція в sidebar теж «Створення додатку».

### Кроки рецепта — labels і gitCheckout

| slug | label | gitCheckout |
|------|-------|-------------|
| `recipes-overview` | Створення додатку | — |
| `recipe-step-01-http` | 1. Перший HTTP-запит | `step-01-http` |
| `recipe-step-02-view` | 2. HTML-сторінки | `step-02-view` |
| `recipe-step-03-validation` | 3. Валідація | `step-03-validation` |
| `recipe-step-04-json-api` | 4. JSON API | `step-04-json-api` |
| `recipe-step-05-session` | 5. Session і CSRF | `step-05-session` |
| `recipe-step-06-database` | 6. База даних | `step-06-database` |
| `recipe-step-07-error-handling` | 7. Обробка помилок | `step-07-error-handling` |
| `recipe-step-08-logging` | 8. Логування і маскування | `step-08-logging` |
| `recipe-step-09-diagnostic-logs` | 9. Діагностичні логи | `step-09-diagnostic-logs` |
| `recipe-step-10-casting` | 10. Casting і DTO | `step-10-casting` |
| `recipe-step-11-config` | 11. Config | `step-11-config` |
| `recipe-step-12-path-manager` | 12. PathManager | `step-12-path-manager` |

Внутрішні теги з префіксами web/api — див. GIT-BRANCHES-TAGS.md; nav може оновлюватись при публікації тегів.

## Плашка `git checkout`

`docs.js` → `renderGitCheckoutBadge()`:

- Тільки якщо `item.gitCheckout` у nav
- Окрема стрічка **над** `<h1>`, badge справа (`align-self: flex-end`)
- Клік копіює команду (ClipboardJS)
- CSS: `.docs-page-header`, `.docs-git-checkout` в `assets/css/docs.css`

Текст: `git checkout {gitCheckout}`

## TOC (правий sidebar «На цій сторінці»)

`renderToc()`:

- Бере `h2`, `h3` з `#docs-content`
- **Без** заголовка «На цій сторінці» / «On this page» (прибрано)
- **Пропускає** headings внутрі inside `.alert` (щоб не з’являлись «Далі», «Вперше з Concept 2?»)
- Якщо немає headings — `.docs-toc-wrap` → `d-none`

## Alert boxes в статтях

- Не використовувати `<h3 class="alert-heading">` якщо текст не потрібен в TOC
- На `recipe-step-01-http` прибрано `<h3>Далі</h3>` — лише `<p>` з посиланнями

## Copy buttons для code blocks

`initCopyButtons()` обгортає `<pre>` у `.docs-code-wrap` + кнопка clipboard.

## Плашка файлу на code block

Якщо в `<h2>` є `<code>` з шляхом/файлом (`public/index.php`, `IndexController.php`), `initFileBadges()` додає **синю плашку** з назвою у **верхньому правому куті** першого `<pre>` після цього заголовка (до наступного `h2`/`h3`).

Альтератива без `h2`: `data-docs-file="path/to/file.php"` на `<pre>`.

CSS: `.docs-file-badge` на `.docs-code-wrap.has-file-badge`. Кнопка copy зміщується нижче плашки.

Приклад HTML (крок рецепта):

```html
<h2 id="step-1-index" class="border-bottom pb-2 mb-3 mt-5">1. <code>public/index.php</code></h2>
<p>…</p>
<pre class="mb-4"><code class="language-php">…</code></pre>
```

Плашка з’являється автоматично — не дублювати path вручну на `<pre>`.

## PHP-лістинги: блок `use`

| Тип лістингу | `use` / `namespace` / `<?php` |
|--------------|-------------------------------|
| Повний файл (migration, seeder, `routes/*.php`, `bin/console.php`, цілий controller-файл) | **так** — щоб copy-paste збігався з репо |
| Фрагмент (метод, шматок Bootstrap, частина класу без файлового каркасу) | **ні** — лише суть кроку; важливі типи — FQCN або ім’я класу в прозі |
| JSON / YAML / bash / Twig | не стосується |

Не змішувати: якщо почали з `<?php` + `namespace` — показуй і `use`; якщо лише `public function submit()` — без імпортів.

## Why first (проза)

Кожен розділ і кожен код-блок у **готових** сторінках (рецепти, Stack bricks, Core з контентом):

1. Спочатку **біль / ситуація** читача (навіщо взагалі цей шар)
2. Потім **коли** це потрібно vs коли ні
3. Лише після цього — API / лістинг

Заборонено сухі однорядкові «X мерджить Y» без контексту. Орієнтир тону: `recipe-step-07-error-handling.html`, `recipe-step-01-http.html`.

Stub-сторінки: короткий **Why first** абзац (біль → коли → посилання) +
примітка «деталі пізніше». Не роздувати до повного гайду.

## Сторінки Stack brick (stack-brick-*)

1. h1 + короткий опис
2. **Таблиця методів** (метод → якір | параметри з типами | опис | залежність)
3. h2 на кожен метод + мінімум один абзац; посилання на Core/Extensions/рецепти — **inline у тексті**
4. Короткий code sample біля методу
5. Не зводити сторінку лише до таблиці без секцій нижче

## Сторінки рецепта — шаблон body

```html
<body data-page="recipe-step-01-http">
```

`data-page` = slug у nav (без `.html`).

## Stub generation

`_generate-stubs.sh` — генерує uk stubs; SKIP_SLUGS для ручних сторінок. Не перезаписує готові.

## Що НЕ в публічній docs

- Треки і відгалуження Web/API (прибрано з recipes-overview)
- Колонка «Трек» в таблиці кроків
- Maintainer symlinks workflow — лише короткий muted paragraph на кроках (dev templates)
- Схеми git branches

Все це — в `/var/www/concept-recipes/agents/`.

## Готові vs stub (uk)

**Готові (Why first):** `index.html`, `recipes-overview.html`, `recipe-step-01-http.html`,
`recipe-step-02-view` … `recipe-step-10-casting`,
`stack-overview`, `stack-bricks`, `stack-brick-*` (http…console, error-handling, logging, masking, casting),
`extension-data-masker.html`,
`architecture.html`, Core section (`core-overview` … `controllers`).

**Stubs з Why-intro (без повного гайду):** `recipe-step-11-config`,
`glossary`, `request-lifecycle`, усі `extension-*` у nav (крім data-masker), `installation`,
`config-reference`, `upgrade-from-1x`.

Орфани поза nav (старі skeleton/recipe-* тощо) — не пріоритет; можна лишати
порожніми або прибрати з диска окремим прибиранням.

## index.html особливості

- `<h1 id="introduction">` = «Вступ» (sync з nav)
- Дубль `<h2>Вступ</h2>` видалено
- `<title>`: «Вступ - Concept 2 Documentation»
- Navbar brand: «Concept 2»

## recipe-step-01-http — запуск (users)

```bash
git clone … concept-recipes && cd concept-recipes
composer install
cp docker-compose.yml.example docker-compose.yml
docker compose up -d --build
curl -s http://localhost:8089/
```

Muted note для maintainers: `docker-compose.dev.yml.example`, `composer-dev.json.example`.

## Посилання між сторінками

- Вступ → «Створення додатку» (`recipes-overview.html`)
- Overview → крок 1
- Крок 1 → крок 2 + overview
