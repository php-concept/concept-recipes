#!/usr/bin/env python3
"""Translate Cyrillic user-facing strings in concept-recipes (skip agents/)."""
from __future__ import annotations

import re
import sys
from pathlib import Path

REPLACEMENTS: list[tuple[str, str]] = [
    ('Concept — Core, Extensions і Stack для PHP 8.4+', 'Concept — Core, Extensions, and Stack for PHP 8.4+'),
    ('Strict-typed PHP&nbsp;8.4+ на PSR: ядро тримає runtime,\n                    extensions дають можливості, stack збирає їх fluent API.',
     'Strict-typed PHP&nbsp;8.4+ on PSR: Core owns the runtime,\n                    Extensions provide capabilities, Stack wires them with a fluent API.'),
    ('Не моноліт «з коробки», а платформа з чіткими межами:\n                обираєш лише те, що потрібно додатку.',
     'Not a batteries-included monolith — a platform with clear boundaries:\n                enable only what the app needs.'),
    ('Тонке ядро: контейнер, маршрутизація, dispatch.\n                        Без config-магії й без залежності від extensions.',
     'A thin core: container, routing, dispatch.\n                        No config magic and no dependency on extensions.'),
    ('Окремі пакети: HTTP, View, Validation, Database, Logging…\n                        Glue передає їм явні параметри.',
     'Separate packages: HTTP, View, Validation, Database, Logging…\n                        Glue passes them explicit parameters.'),
    ('Fluent bricks на кшталт <code>addHttp()</code> і <code>addView()</code> —\n                        збирають обрані providers для контейнера.',
     'Fluent bricks such as <code>addHttp()</code> and <code>addView()</code> —\n                        they assemble the chosen providers for the container.'),
    ('<strong>Concept 2</strong> — runnable recipe surface для документації.',
     '<strong>Concept 2</strong> — runnable recipe surface for the documentation.'),
    ('Після валідації <code>toDto()</code> дає <code>ContactDto</code>\n                (typed поля). CSRF, flashes, errors і old — з ShareViewDataMiddleware.',
     'After validation, <code>toDto()</code> yields a <code>ContactDto</code>\n                (typed fields). CSRF, flashes, errors, and old input come from ShareViewDataMiddleware.'),
    ('Щось пішло не так. У debug (APP_DEBUG=1) буде Whoops; exception також іде в PHP error_log.',
     'Something went wrong. With debug (APP_DEBUG=1) you get Whoops; the exception is also written to the PHP error_log.'),
    ('Recipe boom — Whoops (APP_DEBUG=1) або fallback HTML (APP_DEBUG=0).',
     'Recipe boom — Whoops (APP_DEBUG=1) or fallback HTML (APP_DEBUG=0).'),
    ('Config / PathManager — наступний крок.', 'Config / PathManager — next step.'),
    ('Показати contact submission — Concept 2', 'Show contact submission — Concept 2'),
    ('Перегляд збереженого повідомлення з БД.', 'View a saved submission from the database.'),
    ('Запис з id=<code>{{ id }}</code> не знайдено.', 'No record found for id=<code>{{ id }}</code>.'),
    ('Демо FormRequest — Concept 2', 'FormRequest demo — Concept 2'),
    ('Демо FormRequest → DTO', 'FormRequest → DTO demo'),
    ('Демо валідації', 'Validation demo'),
    ('Як це працює', 'How it works'),
    ('Три шари. Один додаток.', 'Three layers. One app.'),
    ('Від запиту до відповіді', 'From request to response'),
    ('404 — Сторінку не знайдено', '404 — Page not found'),
    ('404 — Не знайдено', '404 — Not found'),
    ('Такої адреси немає в цьому рецепті.', 'This address is not part of this recipe.'),
    ('500 — Помилка сервера', '500 — Server error'),
    ('500 — Внутрішня помилка', '500 — Internal error'),
    ('Ім’я має містити щонайменше 2 символи.', 'Name must be at least 2 characters.'),
    ('Повідомлення має містити щонайменше 10 символів.', 'Message must be at least 10 characters.'),
    ('Вкажіть ім’я.', 'Please enter your name.'),
    ('Вкажіть email.', 'Please enter your email.'),
    ('Напишіть повідомлення.', 'Please enter a message.'),
    ('Некоректний email.', 'Invalid email.'),
    ('На головну', 'Home'),
    ('Надіслати', 'Submit'),
    ('Повідомлення', 'Message'),
    ('Валідація', 'Validation'),
    ('Трійка', 'Trinity'),
    ('збірка', 'assembly'),
    ('можливості', 'capabilities'),
    ('Потік', 'Flow'),
    ('Контакт · Concept 2', 'Contact · Concept 2'),
    ('Контакт', 'Contact'),
    ('Крок 10 · Casting', 'Step 10 · Casting'),
    ('Крок 7 · Обробка помилок', 'Step 7 · Error handling'),
    ('Крок 5 · Session і CSRF', 'Step 5 · Session and CSRF'),
    ('Крок 3 · Валідація', 'Step 3 · Validation'),
    ('Ок.</strong> Дані пройшли валідацію:', 'OK.</strong> Data passed validation:'),
    ('Демо FormRequest', 'FormRequest demo'),
    ('Надішли форму з помилками або валідними даними.', 'Submit the form with invalid or valid data.'),
    ('CSRF ще немає — з’явиться пізніше.', 'CSRF is not enabled yet — it comes later.'),
    ('POST захищений CSRF-токеном у сесії.', 'POST is protected by a CSRF token in the session.'),
    ('Validation через type-hint FormRequest: помилки → flash + redirect.',
     'Validation via FormRequest type-hint: errors → flash + redirect.'),
    ('CSRF, flashes, errors і old приходять з ShareViewDataMiddleware.',
     'CSRF, flashes, errors, and old input come from ShareViewDataMiddleware.'),
    ('Ім’я', 'Name'),
    ("Ім'я", 'Name'),
    ('Logging — окремий крок.', 'Logging — a separate step.'),
    ('// logger: false — Monolog на наступному кроці; поки лише PHP error_log',
     '// logger: false — Monolog on the next step; PHP error_log only for now'),
    ('Diagnostic sinks (validation / SQL) — наступний крок.',
     'Diagnostic sinks (validation / SQL) — next step.'),
    ('<code>{id}</code> у URL приведено до <code>int</code> через',
     '<code>{id}</code> in the URL is cast to <code>int</code> via'),
    ('lang="uk"', 'lang="en"'),
]

SKIP_DIRS = {'agents', 'vendor', 'storage', '.git', 'public/vendor', 'node_modules'}
CYR = re.compile(r'[\u0400-\u04FF]')


def translate_text(text: str) -> str:
    for uk, en in sorted(REPLACEMENTS, key=lambda p: len(p[0]), reverse=True):
        text = text.replace(uk, en)
    text = re.sub(r'Ім.я', 'Name', text)  # any single char between м and я
    return text


def main() -> int:
    root = Path.cwd()
    changed = []
    for path in root.rglob('*'):
        if not path.is_file():
            continue
        rel = path.relative_to(root)
        if any(part in SKIP_DIRS for part in rel.parts):
            continue
        if rel.as_posix().endswith('scripts/translate_en.py'):
            continue
        if path.suffix.lower() not in {'.twig', '.php', '.html', '.md'}:
            continue
        try:
            original = path.read_text(encoding='utf-8')
        except (UnicodeDecodeError, IsADirectoryError):
            continue
        if not CYR.search(original):
            continue
        updated = translate_text(original)
        path.write_text(updated, encoding='utf-8')
        changed.append(str(rel))
        if CYR.search(updated):
            print(f'PARTIAL {rel}', file=sys.stderr)
            for i, line in enumerate(updated.splitlines(), 1):
                if CYR.search(line):
                    print(f'  {i}: {line}', file=sys.stderr)
    for c in changed:
        print(c)
    return 0


if __name__ == '__main__':
    raise SystemExit(main())
