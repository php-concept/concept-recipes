# Додатковий функціонал по типах (Stack bricks + Extensions)

Внутрішній план: як документувати optional fluent API на кшталт `HttpBuilder::withTypedRouteParameters()` (скрін IDE), без роздування рецептів.

## Проблема

`$stack->add(new HttpBrick())->builder()` дає **HttpBuilder** з методами, які не входять у крок 1 рецепта
(див. [STACK-BUILDER-NAMING.md](./STACK-BUILDER-NAMING.md)):

| Метод | Що робить | Залежність |
|-------|-----------|------------|
| `setRoutes()` | route files | **базовий** (крок 1) |
| `setInterceptors()` | Route interceptors | core contracts |
| `withFormRequests()` | FormRequest resolving | `ValidationBrick` |
| `withTypedRouteParameters()` | casting route params | `CastingBrick` |
| `setNotFoundMiddleware()` | 404 middleware | — |

Рецепт показує **мінімум** для першого результату. IDE показує **весь brick** — це має жити в довіднику, не в кожному крокі.

## Три шари документації (не змішувати)

```
┌─────────────────────────────────────────────────────────────┐
│  Створення додатку (recipes)                                │
│  Мінімум: один brick + 1–2 builder-методи для цього кроку   │
└─────────────────────────────────────────────────────────────┘
                              ↓ «деталі API»
┌─────────────────────────────────────────────────────────────┐
│  Stack → Bricks (stack-brick-*)                             │
│  Повний fluent API builder-а: всі методи, deps, defaults    │
└─────────────────────────────────────────────────────────────┘
                              ↓ «що під капотом»
┌─────────────────────────────────────────────────────────────┐
│  Extensions → extension-*                                   │
│  Пакет: ServiceProvider, contracts, класи, wiring rules     │
└─────────────────────────────────────────────────────────────┘
```

### 1. Рецепти (`recipe-step-*`)

- Лише код, потрібний **для цього кроку**
- Приклад крок 1: `add(new HttpBrick())->builder()->setRoutes([...])` — більше нічого
- Крок 3: `ValidationBrick` для factory; `withFormRequests()` — лише якщо type-hint FormRequest у action
- Внизу кроку: «Додаткові опції Http — [Stack: Http brick]»

**Не** переносити в рецепт full autocomplete-список.

### 2. Stack bricks (stack-brick-*)

**Одна сторінка = один builder** (паралельно з capability):

| Slug | Builder | Entry |
|------|---------|-------|
| `stack-brick-http` | `HttpBuilder` | `add(new HttpBrick())` |
| `stack-brick-view` | `ViewBuilder` → Twig/Plates | `add(new ViewBrick())` |
| `stack-brick-validation` | `ValidationBuilder` | `add(new ValidationBrick())` |
| `stack-brick-session` | `SessionBuilder` | `add(new SessionBrick())` |
| `stack-brick-database` | `DatabaseBuilder` | `add(new DatabaseBrick())` |
| `stack-brick-logging` | `LoggingBuilder` | `add(new LoggingBrick())` |
| `stack-brick-masking` | `MaskingBuilder` | `add(new MaskingBrick())` |
| … | … | … |

**Шаблон сторінки brick:**

1. **h1 + короткий опис** capability (`*Brick` → `builder()`)
2. **Таблиця методів** builder-а: метод → якір | **параметри (PHP types)** | опис | залежність
3. **Окремий h2 на кожен метод builder-а** — мінімум один абзац; inline-посилання
4. Короткий code sample на метод (де доречно)
5. Не повторювати загальну модель Stack (`providers()`, lifecycle) — посилання на `stack-overview`
6. Не дублювати повний рецепт — лише glue API brick-а

`stack-bricks.html` залишається **індекс**: таблиця всіх bricks → посилання на `stack-brick-*`.

### 3. Extensions (`extension-http`, …)

**Пакет**, не fluent API:

- `HttpServiceProvider`, constructor params
- `ResponseFactory`, `UrlGenerator`, contracts
- Що extension **не** робить (не routes, не interceptors — це Stack/Core glue)

Посилання вгорі: «Збірка в Stack: [Http brick]».

## HTTP — конкретний приклад

### Крок 1 рецепта (вже є)

```php
$stack->add(new HttpBrick())
    ->builder()
    ->setRoutes([$rootPath . 'routes/web.php']);
```

### `stack-brick-http.html` (довідник)

Документує всі методи з `HttpBuilder` (цільові імена):

- `setRoutes(array $routes)` — обов’язково для web app
- `setInterceptors(array)` — → Core `RouteInterceptorInterface` (instance або Closure factory)
- `withFormRequests()` — requires `Capability::VALIDATION`
- `withTypedRouteParameters()` — requires `Capability::CASTING`
- `setNotFoundMiddleware($middleware)` — custom 404 (instance або Closure; не class-string)

Приклад комбінованої збірки (не рецепт):

```php
$stack->add(new CastingBrick())->builder()/* … */;
$stack->add(new ValidationBrick())->builder()/* … */;

$stack->add(new HttpBrick())
    ->builder()
    ->setRoutes([$rootPath . 'routes/web.php'])
    ->setInterceptors([
        static fn (ContainerInterface $c) => $c->get(AuthInterceptor::class),
    ])
    ->withTypedRouteParameters()
    ->withFormRequests()
    ->setNotFoundMiddleware(
        static fn (ContainerInterface $c) => $c->get(NotFoundJsonMiddleware::class),
    );
```

Instance / factory Closure; class-string не підтримується.
### `extension-http.html`

- `Concept\Extensions\Http\HttpServiceProvider`
- `Response\ResponseFactory`, URL generator
- PSR-7 response helpers
- Без `setRoutes()` — це Stack `HttpOptions`

## Навігація (план змін nav-uk.js)

**Варіант A — підсекція в Stack (рекомендовано):**

```
Stack
  Огляд ConceptStack
  Bricks                    ← індекс
  Http brick
  View brick
  Validation brick
  …
  Життєвий цикл запиту
```

**Варіант B — brick-методи як h2 на `stack-bricks`:**

Одна довга сторінка; TOC праворуч по brick-ам. Менше файлів, але росте до нечитабельності.

**Рекомендація:** Варіант A після 3–4 bricks; поки HTTP один — можна почати з секції на `stack-bricks.html` (h2 «Http brick»), потім винести в `stack-brick-http.html`.

Extensions nav **без** brick-методів — лише пакети.

## concept-recipes

Optional brick-методи з’являються в recipes **лише коли крок їх вводить**:

| Крок | Stack brick додатково |
|------|------------------------|
| 1 | `setRoutes()` |
| 3 | `ValidationBrick`; `withFormRequests()` лише для type-hint у action |
| 3+ / 10 | `CastingBrick` + `withTypedRouteParameters()` — опційний крок 10 (DTO / typed routes) |
| 4 JSON | `setNotFoundMiddleware()` для API — опційно |
| 5 | `setInterceptors()` — якщо треба до session |

Не робити «повний stack» у recipes до кроку 8.

## Синхронізація з кодом Stack

Джерело правди для таблиць методів: `concept-stack/src/Bricks/{Name}/{Name}Builder.php`.

При додаванні методу в builder:

1. PHPDoc у PHP (вже є)
2. Рядок у `stack-brick-*.html`
3. Рядок у індексі `stack-bricks.html`
4. Якщо метод входить у рецепт — крок рецепта + (опційно) тег у recipes

## Порядок наповнення (HTTP першим)

1. `stack-bricks.html` — індекс + секція **Http brick** (таблиця методів)
2. `extension-http.html` — пакет extension-http
3. Крок 3 рецепта — `addValidation()` + factory; `withFormRequests()` згадати як опцію type-hint
4. Потім View brick, Validation brick, …

## Що не робити

- Не дублювати full brick API в кожному recipe-step
- Не класти brick fluent API в extension-* (плутанина пакет vs Stack)
- Не показувати треки/відгалуження в публічних brick-сторінках (agents only)
