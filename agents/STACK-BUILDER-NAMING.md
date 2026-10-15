# Конвенція неймінгу Stack / Bricks: add / set / with

Три префікси з чіткими ролями. Bare nouns (`routes()`, `notFound()`) не використовуємо.

Пріоритет: **легко читати і писати**. `set*` — assignment; `with*` — opt-in; `add` — реєстрація brick у стеку.

## Правила

| Префікс | Роль | Де | Приклад |
|---------|------|-----|---------|
| **`add`** | Зареєструвати brick у стеку | лише `StackBuilder` | `$stack->add(new HttpBrick())` |
| **`set*`** | Призначити значення / список / handler | brick builder | `->setRoutes([...])` |
| **`with*`** | Увімкнути opt-in feature або sub-capability | brick builder | `->withFormRequests()`, `->withTwig()` |

### `add` — тільки на `StackBuilder`

- Thin stack (`php-concept/stack` ^2): **немає** `addHttp()` / `addView()` / …
- First-party capabilities — пакети `php-concept/brick-*`
- Escape hatch: `addProvider(...)`
- Після `add()` викликай `->builder()` для fluent конфігу

Сенс: «додай brick до збірки».

### `set*` — конфіг на brick builder

Передати дані без увімкнення нової capability:

- списки: routes, interceptors, seeders, commands
- об’єкти: handler, middleware, connection array
- скаляри/опції: channel, level, session options, debug flag

### `with*` — opt-in

- `withFormRequests()` → require Validation
- `withTypedRouteParameters()` → require Casting
- `withMasking()`, `withCsrf()`
- sub-builder: `withTwig()`, `withPlates()`

---

## Http — цільовий API

```php
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

Instance або `Closure(ContainerInterface)` — не class-string (Core більше не резолвить рядки).

| Метод | Префікс | Чому |
|-------|---------|------|
| `add(new HttpBrick())` | add | entry capability |
| `builder()` | — | доступ до fluent HttpBuilder |
| `setRoutes(...)` | set | присвоєння шляхів |
| `setInterceptors(...)` | set | присвоєння списку |
| `withFormRequests()` | with | opt-in + Validation |
| `withTypedRouteParameters()` | with | opt-in + Casting |
| `setNotFoundMiddleware(...)` | set | присвоєння middleware |

---

## Пакети bricks (monorepo `concept-bricks`)

| Capability | Пакет | Клас |
|------------|-------|------|
| HTTP | `brick-http` | `HttpBrick` |
| View | `brick-view` | `ViewBrick` |
| Validation | `brick-validation-rakit` | `ValidationBrick` |
| Session | `brick-session-symfony` | `SessionBrick` |
| Database | `brick-database-eloquent` | `DatabaseBrick` |
| Logging | `brick-logger-monolog` | `LoggingBrick` |
| Masking | `brick-data-masker` | `MaskingBrick` |
| Error handling | `brick-error-handler-whoops` | `ErrorHandlingBrick` |
| Casting | `brick-casting-valinor` | `CastingBrick` |
| Console | `brick-console-symfony` | `ConsoleBrick` |
| … | … | … |

Builder-методи (`set*` / `with*` / `to*` sinks) — без змін семантики відносно попереднього StackBuilder fluent API; змінився лише **entry**.

### Виняток: `to*` для sinks (Logging)

- `toRotatingFile()`, `toStderr()`
- Escape hatch: `withHandler()`

---

## Мінімальний рецепт (крок 1)

```php
$stack->add(new HttpBrick())
    ->builder()
    ->setRoutes([$rootPath . 'routes/web.php']);
```

## Читабельність

```php
$stack->add(new HttpBrick())
    ->builder()
    ->setRoutes([$rootPath . 'routes/web.php'])
    ->withFormRequests();

$stack->add(new ValidationBrick())
    ->builder()
    ->setRules([...])
    ->withMasking();
```

## Foundation (не bricks)

`extension-config`, `extension-path-manager` — окремо в `FoundationBootstrap`, не через `ConceptStack::add()`.

## Документація

- Brick pages: повний fluent API builder-а
- Рецепти: мінімум `add(new HttpBrick())->builder()->setRoutes(...)`
- Не згадувати застарілі `addHttp()` / `withHttp()` на StackBuilder
