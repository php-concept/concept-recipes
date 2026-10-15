# agents — контекст для агента / maintainer

Документи тут збирають **внутрішній контекст** проєкту `concept-recipes` і зв’язаної документації. Публічна docs **не** дублює все з цих файлов (наприклад, треки/відгалуження — лише тут).

| Файл | Зміст |
|------|--------|
| [CONTEXT.md](./CONTEXT.md) | Екосистема Concept 2, два репо, layout `/var/www/` |
| [RECIPES-STEPS.md](./RECIPES-STEPS.md) | 8 кроків рецепта: код, Stack, composer, файли |
| [GIT-BRANCHES-TAGS.md](./GIT-BRANCHES-TAGS.md) | Гілки `track/web`, `track/api`, теги, відгалуження |
| [DOCKER-COMPOSER.md](./DOCKER-COMPOSER.md) | Users vs maintainers: docker, composer.json / composer-dev.json |
| [DOCS-CONVENTIONS.md](./DOCS-CONVENTIONS.md) | `concept-docs-2`: nav, плашки git, заголовки, TOC |
| [EXTENSION-AND-STACK-DOCS.md](./EXTENSION-AND-STACK-DOCS.md) | Рецепти vs Stack bricks vs Extensions (optional API) |
| [STACK-BUILDER-NAMING.md](./STACK-BUILDER-NAMING.md) | Конвенція неймінгу `add*` / `set*` / `with*` (+ `to*` sinks) |
| [ROADMAP.md](./ROADMAP.md) | План розвитку recipes + docs по етапах |

**Швидкі посилання**

- Документація (статичний сайт): `/var/www/concept-docs-2/`
- Живий код рецептів: `/var/www/concept-recipes/` (цей репо)
- Core: `/var/www/concept-core-2/`
- Stack: `/var/www/concept-stack/`
- Extensions: `/var/www/concept-extensions/`

**Стан на момент створення нотаток:** крок 1 реалізований локально; **git без комітів**; контейнер `concept_recipes` на `:8089`.
