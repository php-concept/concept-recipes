#!/usr/bin/env bash
# Rebase helper: rename path repo support → extension-support in composer-dev.json.example.
set -euo pipefail

FILE="composer-dev.json.example"
[[ -f "$FILE" ]] || exit 0
grep -qF '../concept-extensions/support' "$FILE" || exit 0

python3 <<'PY'
from pathlib import Path
import re
import subprocess

path = Path("composer-dev.json.example")
text = path.read_text()
new = text.replace(
    "../concept-extensions/support",
    "../concept-extensions/extension-support",
)
# Exact package name only — avoid turning extension-support into extension-extension-support.
new = re.sub(r'"php-concept/support"', '"php-concept/extension-support"', new)
if new == text:
    raise SystemExit(0)
path.write_text(new)
subprocess.run(["git", "add", str(path)], check=True)
subprocess.run(["git", "commit", "--amend", "--no-edit"], check=True)
PY
