#!/usr/bin/env bash
# Rebase helper: amend step-10 — withTypedRouteParameters() on Http.
set -euo pipefail
ROOT="$(git rev-parse --show-toplevel)"
cd "$ROOT"
MSG=$(git log -1 --format=%s)

case "$MSG" in
  *step-10-casting*)
    python3 <<'PY'
from pathlib import Path

bootstrap = Path("src/App/Bootstrap/ApplicationStackBootstrap.php")
text = bootstrap.read_text()
text = text.replace(
    " * Stack recipe: Casting — FormRequest → DTO (toDto).\n",
    " * Stack recipe: Casting — FormRequest → DTO (toDto), typed route parameters.\n",
)
text = text.replace(
    "            ->withFormRequests();\n",
    "            ->withFormRequests()\n            ->withTypedRouteParameters();\n",
    1,
)
bootstrap.write_text(text)

controller = Path("src/App/Controllers/ContactController.php")
text = controller.read_text()
text = text.replace(
    "     * Show one submission by id (route {id} → int via Core Reflection coercion).\n",
    "     * Show one submission by id ({id} → int via withTypedRouteParameters + Casting).\n",
)
controller.write_text(text)
PY
    git add src/App/Bootstrap/ApplicationStackBootstrap.php src/App/Controllers/ContactController.php
    git commit --amend --no-edit
    ;;
esac
