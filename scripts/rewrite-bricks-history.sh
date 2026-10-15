#!/usr/bin/env bash
# Rebuild main: Initial + 12 step commits with brick API; retag step-*.
# Does NOT force-push. Source trees = current step-* tags (pre-rewrite).
set -euo pipefail

cd "$(git rev-parse --show-toplevel)"

PYTHON="${PYTHON:-python3}"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
TRANSFORM="$SCRIPT_DIR/rewrite-bricks-history.py"
WORK="${REWRITE_WORK:-/tmp/recipes-rewrite}"
AGENTS_SRC="${WORK}/tip-worktree/agents"
TIP_SRC="${WORK}/tip-worktree"

if [[ ! -f "$TRANSFORM" ]]; then
  echo "missing $TRANSFORM" >&2
  exit 1
fi
if [[ ! -d "$AGENTS_SRC" ]]; then
  echo "missing tip agents at $AGENTS_SRC — save tip worktree first" >&2
  exit 1
fi

# Capture OLD tag commits BEFORE we move tags
declare -A OLD_TAGS=(
  [step-01-http]="$(git rev-parse step-01-http)"
  [step-02-view]="$(git rev-parse step-02-view)"
  [step-03-validation]="$(git rev-parse step-03-validation)"
  [step-04-json-api]="$(git rev-parse step-04-json-api)"
  [step-05-session]="$(git rev-parse step-05-session)"
  [step-06-database]="$(git rev-parse step-06-database)"
  [step-07-error-handling]="$(git rev-parse step-07-error-handling)"
  [step-08-logging]="$(git rev-parse step-08-logging)"
  [step-09-diagnostic-logs]="$(git rev-parse step-09-diagnostic-logs)"
  [step-10-casting]="$(git rev-parse step-10-casting)"
  [step-11-config]="$(git rev-parse step-11-config)"
  [step-12-path-manager]="$(git rev-parse step-12-path-manager)"
)
INITIAL="$(git rev-parse 4449038)"

STEPS=(
  "step-01-http|First HTTP request"
  "step-02-view|HTML pages and Twig"
  "step-03-validation|Validation and FormRequest"
  "step-04-json-api|JSON API"
  "step-05-session|Session, CSRF, and flash messages"
  "step-06-database|Database, migrations, and MySQL"
  "step-07-error-handling|Error handling with Whoops"
  "step-08-logging|Logging with Monolog and masking"
  "step-09-diagnostic-logs|Diagnostic validation and query logs"
  "step-10-casting|Casting and DTO"
  "step-11-config|Config"
  "step-12-path-manager|PathManager"
)

tmp="rewrite-bricks-$$"
echo "Creating orphan branch $tmp from initial $INITIAL"

# Preserve current branch tip ref for safety
git branch "backup/pre-bricks-rewrite-$(date +%Y%m%d%H%M%S)" HEAD

git checkout --orphan "$tmp"
git reset --hard
git clean -fdx -e '/vendor' -e '/.env' -e '/composer-dev.json' -e '/composer-dev.lock' -e '/docker-compose.yml' -e '/storage' -e '/public/vendor' -e '/.idea'

# --- Initial commit (skeleton + stack ^2) ---
git read-tree --reset -u "$INITIAL"
rm -rf agents
cp -a "$AGENTS_SRC" agents
"$PYTHON" "$TRANSFORM" --root .
# Refresh lock for skeleton (core + stack only)
if command -v composer >/dev/null; then
  rm -f composer.lock
  composer update --no-interaction --no-ansi 2>/dev/null | tail -5 || true
fi
git add -A
# Keep vendor out
git reset HEAD -- vendor public/vendor composer-dev.json composer-dev.lock docker-compose.yml .env 2>/dev/null || true
git add -A
git status --short | head -40
git commit -m "Initial commit"

refresh_lock() {
  if ! command -v composer >/dev/null; then
    return 0
  fi
  rm -f composer.lock
  # Prefer Packagist lock for readers; ignore failures on unpublished packages
  if ! composer update --no-interaction --no-ansi >/tmp/recipes-composer-update.log 2>&1; then
    echo "  ! composer update failed — keeping json without fresh lock (see /tmp/recipes-composer-update.log)"
    return 0
  fi
  git add composer.lock 2>/dev/null || true
}

for entry in "${STEPS[@]}"; do
  IFS='|' read -r tag title <<< "$entry"
  old="${OLD_TAGS[$tag]}"
  echo "→ $tag from $old"

  git read-tree --reset -u "$old"
  rm -rf agents
  cp -a "$AGENTS_SRC" agents

  # Step 12: prefer already-migrated tip bootstrap + composer from worktree
  if [[ "$tag" == "step-12-path-manager" ]]; then
    cp -a "$TIP_SRC/src/App/Bootstrap/ApplicationStackBootstrap.php" src/App/Bootstrap/ApplicationStackBootstrap.php
    cp -a "$TIP_SRC/composer.json" composer.json
    cp -a "$TIP_SRC/composer-dev.json.example" composer-dev.json.example
    cp -a "$TIP_SRC/docker-compose.dev.yml.example" docker-compose.dev.yml.example
    if [[ -f "$TIP_SRC/composer.lock" ]]; then
      cp -a "$TIP_SRC/composer.lock" composer.lock
    fi
    if [[ -f "$TIP_SRC/README.md" ]]; then
      cp -a "$TIP_SRC/README.md" README.md
    fi
  else
    "$PYTHON" "$TRANSFORM" --root .
    refresh_lock
  fi

  git add -A
  git reset HEAD -- vendor public/vendor composer-dev.json composer-dev.lock docker-compose.yml .env 2>/dev/null || true
  # Ensure ignored runtime stays out
  git add -u
  git add agents src composer.json composer.lock composer-dev.json.example docker-compose.dev.yml.example README.md 2>/dev/null || true
  git commit -m "${title} (${tag})"
  git tag -f "$tag"
done

# Tip maintainer commit: scripts from tip (rewrite helpers etc.)
echo "→ tip maintainer overlay"
rsync -a \
  --exclude .git --exclude vendor --exclude storage --exclude public/vendor \
  --exclude composer-dev.json --exclude composer-dev.lock --exclude docker-compose.yml --exclude .env \
  --exclude .idea \
  "$TIP_SRC"/ ./
# Keep step-12 tree as base; overlay tip scripts/agents/README already applied
git add -A
git reset HEAD -- vendor public/vendor composer-dev.json composer-dev.lock docker-compose.yml .env 2>/dev/null || true
if ! git diff --cached --quiet; then
  git commit -m "Migrate recipes to thin stack and brick-* packages"
else
  echo "  (no tip delta beyond step-12)"
fi

git branch -M main
echo ""
echo "Done. Log:"
git log --oneline | head -20
echo ""
echo "Tags:"
git tag -l 'step-*'
echo ""
echo "Backup branch kept. Force-push when ready:"
echo "  git push --force-with-lease origin main"
echo "  git push --force origin 'refs/tags/step-*'"
