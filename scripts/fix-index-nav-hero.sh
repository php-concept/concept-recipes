#!/usr/bin/env bash
# Rebase helper: promo nav + hero CTA on index.twig (GitHub + Contact).
set -euo pipefail

FILE="resources/views/index.twig"
[[ -f "$FILE" ]] || exit 0

python3 <<'PY'
from pathlib import Path
import subprocess

path = Path("resources/views/index.twig")
text = path.read_text()
new = text

github_btn = """<a class="btn-promo btn-promo-ghost" href="https://github.com/php-concept/concept-recipes" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-github me-1" aria-hidden="true"></i> GitHub
                    </a>"""

replacements = [
    (
        """            <div class="d-flex gap-2">
                <a class="btn-promo btn-promo-ghost" href="/contact">Validation</a>
                <a class="btn-promo btn-promo-ghost" href="#trinity">Trinity</a>
            </div>""",
        """            <a class="btn-promo btn-promo-ghost" href="/contact">Contact</a>""",
    ),
    (
        '<a class="btn-promo btn-promo-ghost" href="/contact">Validation demo</a>',
        github_btn,
    ),
    (
        '<a class="btn-promo btn-promo-ghost" href="https://github.com/php-concept" rel="noopener">',
        '<a class="btn-promo btn-promo-ghost" href="https://github.com/php-concept/concept-recipes" target="_blank" rel="noopener noreferrer">',
    ),
    (
        """            <a class="btn-promo btn-promo-ghost" href="#trinity">Trinity</a>
""",
        "",
    ),
]

for old, repl in replacements:
    new = new.replace(old, repl)

if new == text:
    raise SystemExit(0)

path.write_text(new)
subprocess.run(["git", "add", str(path)], check=True)
subprocess.run(["git", "commit", "--amend", "--no-edit"], check=True)
PY
