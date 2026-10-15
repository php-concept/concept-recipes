#!/usr/bin/env bash
# Rebase helper: remove setCacheDir and keep valid PHP chaining.
set -euo pipefail

FILE="src/App/Bootstrap/ApplicationStackBootstrap.php"

[[ -f "$FILE" ]] || exit 0

python3 <<'PY'
from pathlib import Path
import subprocess

path = Path("src/App/Bootstrap/ApplicationStackBootstrap.php")
text = path.read_text()
new = text
new = new.replace(
    "            ->setViewsPath($rootPath . 'resources/views')\n"
    "            ->setCacheDir($rootPath . 'storage/cache/views');",
    "            ->setViewsPath($rootPath . 'resources/views');",
)
new = new.replace(
    "\n            ->setCacheDir($rootPath . 'storage/cache/casting')",
    "",
)
new = new.replace(
    "            ->setViewsPath($rootPath . 'resources/views')\n",
    "            ->setViewsPath($rootPath . 'resources/views');\n",
)
if new == text:
    raise SystemExit(0)
path.write_text(new)
subprocess.run(["git", "add", str(path)], check=True)
subprocess.run(["git", "commit", "--amend", "--no-edit"], check=True)
PY
