#!/usr/bin/env bash
# Rebase helper: setHandler → setHandlerFactory in ApplicationStackBootstrap.php
set -euo pipefail

FILE="src/App/Bootstrap/ApplicationStackBootstrap.php"

if [[ ! -f "$FILE" ]] || ! grep -qF 'setHandler(' "$FILE"; then
  exit 0
fi

python3 <<'PY'
from pathlib import Path
import subprocess

path = Path("src/App/Bootstrap/ApplicationStackBootstrap.php")
text = path.read_text()
old = "->setHandler(new NativeFileSessionHandler($rootPath . 'storage/sessions'))"
new = """->setHandlerFactory(function() use ($rootPath) {
                return new NativeFileSessionHandler($rootPath . 'storage/sessions');
            })"""
if old not in text:
    raise SystemExit(0)
path.write_text(text.replace(old, new))
subprocess.run(["git", "add", str(path)], check=True)
subprocess.run(["git", "commit", "--amend", "--no-edit"], check=True)
PY
