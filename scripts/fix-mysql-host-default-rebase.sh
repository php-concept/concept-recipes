#!/usr/bin/env bash
# Rebase helper: default MYSQL_HOST → concept_recipes_db (Docker Compose container_name).
# Applies on steps 6–10 (getenv era); step 11+ uses Config / DB_HOST from .env.
set -euo pipefail

FILE="src/App/Bootstrap/ApplicationStackBootstrap.php"
[[ -f "$FILE" ]] || exit 0
grep -qF "getenv('MYSQL_HOST') ?: '127.0.0.1'" "$FILE" || exit 0

python3 <<'PY'
from pathlib import Path
import subprocess

path = Path("src/App/Bootstrap/ApplicationStackBootstrap.php")
text = path.read_text()
old = "getenv('MYSQL_HOST') ?: '127.0.0.1'"
new = "getenv('MYSQL_HOST') ?: 'concept_recipes_db'"
if old not in text:
    raise SystemExit(0)
path.write_text(text.replace(old, new))
subprocess.run(["git", "add", str(path)], check=True)
subprocess.run(["git", "commit", "--amend", "--no-edit"], check=True)
PY
