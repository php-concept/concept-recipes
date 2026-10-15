#!/usr/bin/env bash
# Rebase helper: add Stats nav link when /api/stats exists (step 6+).
set -euo pipefail

API_ROUTES="routes/api.php"
[[ -f "$API_ROUTES" ]] || exit 0
grep -qF "'/stats'" "$API_ROUTES" || grep -qF '"/stats"' "$API_ROUTES" || exit 0

python3 <<'PY'
from pathlib import Path
import subprocess

stats_link = (
    '<a class="btn-promo btn-promo-ghost" href="/api/stats" '
    'target="_blank" rel="noopener noreferrer">Stats</a>'
)

def patch(path: Path, old: str, new: str) -> bool:
    if not path.exists():
        return False
    text = path.read_text()
    if "/api/stats" in text:
        return False
    if old not in text:
        return False
    path.write_text(text.replace(old, new, 1))
    return True

changed = False

index_old = '            <a class="btn-promo btn-promo-ghost" href="/contact">Contact</a>'
index_new = f"""            <div class="d-flex gap-2">
                <a class="btn-promo btn-promo-ghost" href="/contact">Contact</a>
                {stats_link}
            </div>"""
if patch(Path("resources/views/index.twig"), index_old, index_new):
    changed = True

contact_old = '            <a class="btn-promo btn-promo-ghost" href="/">Home</a>'
contact_new = f"""            <div class="d-flex gap-2">
                <a class="btn-promo btn-promo-ghost" href="/">Home</a>
                {stats_link}
            </div>"""
if patch(Path("resources/views/contact.twig"), contact_old, contact_new):
    changed = True

show_old = '            <a class="btn-promo btn-promo-ghost" href="/contact">Contact</a>'
show_new = f"""            <div class="d-flex gap-2">
                <a class="btn-promo btn-promo-ghost" href="/">Home</a>
                <a class="btn-promo btn-promo-ghost" href="/contact">Contact</a>
                {stats_link}
            </div>"""
if patch(Path("resources/views/contact-show.twig"), show_old, show_new):
    changed = True

if not changed:
    raise SystemExit(0)

subprocess.run(["git", "add", "-u", "resources/views"], check=True)
subprocess.run(["git", "commit", "--amend", "--no-edit"], check=True)
PY
