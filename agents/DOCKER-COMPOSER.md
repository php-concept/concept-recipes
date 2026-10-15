# Docker і Composer: users vs maintainers

## Два workflow

| | **Users** (читачі docs) | **Maintainers** (ми) |
|---|-------------------------|------------------------|
| Composer | `composer.json` → Packagist | `composer-dev.json` → path repos + symlinks |
| Lock | `composer.lock` (commit) | `composer-dev.lock` (gitignored) |
| Docker template | `docker-compose.yml.example` | `docker-compose.dev.yml.example` |
| Локальний compose | `cp … docker-compose.yml` (gitignored) | same from dev example |
| `COMPOSER` env у контейнері | не задано | `composer-dev.json` |
| Sibling mounts | ні | `concept-core-2`, `concept-stack`, `concept-extensions` |

## Users — quick start

```bash
git clone … concept-recipes && cd concept-recipes
composer install
cp docker-compose.yml.example docker-compose.yml
docker compose up -d --build
curl -s http://localhost:8089/
```

Документація (`minimal-bootstrap.html`) описує **цей** flow, не `composer-dev.json`.

## Maintainers — local DX

```bash
cp docker-compose.dev.yml.example docker-compose.yml
cp composer-dev.json.example composer-dev.json
COMPOSER=composer-dev.json composer install
docker compose up -d --build
```

Layout:

```
/var/www/
  concept-core-2/
  concept-stack/
  concept-extensions/
  concept-recipes/
```

Зміни PHP у sibling repos одразу в контейнері через symlinks у `vendor/php-concept/*` (без `composer update` для коду).

## Docker files

| Файл | В репо | Опис |
|------|--------|------|
| `Dockerfile` | yes | `php:8.4-apache`, dom/pdo/mysql, composer |
| `docker-compose.yml.example` | yes | app only, port 8089, vhost + php ini |
| `docker-compose.dev.yml.example` | yes | + sibling mounts + `COMPOSER=composer-dev.json` |
| `docker-compose.yml` | **gitignored** | локальна копія |
| `docker/apache/vhost.conf` | yes | `DocumentRoot` → `public/` |
| `docker/php/error-reporting.ini` | yes | `E_ALL`, log to apache error.log |

### docker-compose.yml.example (users)

```yaml
services:
  app:
    build: .
    container_name: concept_recipes
    user: "${UID:-1000}:${GID:-1000}"
    ports:
      - "8089:80"
    volumes:
      - .:/var/www/html
      - ./docker/apache/vhost.conf:/etc/apache2/sites-available/000-default.conf
      - ./docker/php/error-reporting.ini:/usr/local/etc/php/conf.d/error-reporting.ini
```

### docker-compose.dev.yml.example (maintainers)

Додатково:

```yaml
volumes:
  - ../concept-core-2:/var/www/concept-core-2
  - ../concept-stack:/var/www/concept-stack
  - ../concept-extensions:/var/www/concept-extensions
environment:
  - COMPOSER=composer-dev.json
```

Path repos у `composer-dev.json` використовують `../concept-*` від `/var/www/html` → `/var/www/concept-*` у контейнері.

## composer.json (users, committed)

- `name`: `php-concept/recipes`
- `require`: крок-залежні пакети з Packagist
- `autoload`: `Concept\\` → `src/` (**обов’язково** — без autoload `ApplicationStackBootstrap` не знаходиться)

Крок 1:

```json
"php-concept/core-2": "^1.0",
"php-concept/stack": "^1.0",
"php-concept/extension-http": "^1.0"
```

## composer-dev.json (maintainer, gitignored)

- Копія з `composer-dev.json.example`
- Всі extension path repos (symlink true)
- `php-concept/stack`: `@dev`
- `require-dev`: phpstan
- `scripts.boot-smoke`: `php bin/boot-smoke.php` (**файл ще не створений**)

## .gitignore (concept-recipes)

```
/.idea
/vendor/
/.env
/composer-dev.json
/composer-dev.lock
/docker-compose.yml
/README.md
```

`composer-dev.json.example`, `docker-compose.*.example` — **в репо**.

`README.md` gitignored — локальні нотатки; публічний quick start у docs.

## Крок 6 — план Docker

Додати `db` service (орієнтир: `concept-skeleton-dev-2`):

- `mysql:8.4`, `MYSQL_ROOT_PASSWORD`, port map (наприклад `3367:3306` щоб не конфліктувати)
- `depends_on`, `MYSQL_HOST=db` у app
- Лише в example files; users/dev копіюють у локальний compose

## Перевірка без Docker

```bash
cd /var/www/concept-recipes
composer install   # або COMPOSER=composer-dev.json composer install
php -r 'require "vendor/autoload.php"; $app = require "bootstrap/app.php"; echo $app->handle()->getBody();'
# Hello, world!
```

## Container

- Name: `concept_recipes`
- URL: `http://localhost:8089/`
- User in container: `${UID:-1000}:${GID:-1000}`
