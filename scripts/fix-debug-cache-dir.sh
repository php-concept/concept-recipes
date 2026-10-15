#!/usr/bin/env bash
# Rebase helper: setCacheDir + setDebug($debug) when $debug exists (step 7+).
set -euo pipefail

FILE="src/App/Bootstrap/ApplicationStackBootstrap.php"
[[ -f "$FILE" ]] || exit 0
grep -qF '$debug = getenv' "$FILE" || exit 0

python3 <<'PY'
from pathlib import Path
import subprocess

path = Path("src/App/Bootstrap/ApplicationStackBootstrap.php")
text = path.read_text()
new = text

view_old = """        $stack->addView()
            ->withTwig()
            ->setViewsPath($rootPath . 'resources/views');"""

view_new = """        $stack->addView()
            ->withTwig()
            ->setViewsPath($rootPath . 'resources/views')
            ->setCacheDir($rootPath . 'storage/cache/views')
            ->setDebug($debug);"""

if view_old in new and "->setDebug($debug)" not in new.split("addView()")[1].split("addConsole()")[0]:
    new = new.replace(view_old, view_new, 1)

casting_old = """        $stack->addCasting()
            ->setDebug($debug);"""

casting_new = """        $stack->addCasting()
            ->setCacheDir($rootPath . 'storage/cache/casting')
            ->setDebug($debug);"""

if casting_old in new:
    new = new.replace(casting_old, casting_new, 1)

if new == text:
    raise SystemExit(0)

path.write_text(new)
subprocess.run(["git", "add", str(path)], check=True)
subprocess.run(["git", "commit", "--amend", "--no-edit"], check=True)
PY
