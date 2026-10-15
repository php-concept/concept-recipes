#!/usr/bin/env bash
# Legacy: rebuild linear main history from old source commits (one-time migration).
# Day-to-day: edit step commits on main + retag step-* (see agents/GIT-BRANCHES-TAGS.md).
set -euo pipefail

cd "$(git rev-parse --show-toplevel)"

skeleton_dir="${1:-}"

steps=(
  "step-01-http|First HTTP request|f1bfc33"
  "step-02-view|HTML pages and Twig|2eeca7b"
  "step-03-validation|Validation and FormRequest|b960294"
  "step-04-json-api|JSON API|300f2ed"
  "step-05-session|Session, CSRF, and flash messages|1f5be1b"
  "step-06-database|Database, migrations, and MySQL|510da1e"
  "step-07-error-handling|Error handling with Whoops|932c2cb"
  "step-08-logging|Logging with Monolog and masking|007beba"
  "step-09-diagnostic-logs|Diagnostic validation and query logs|b075eb0"
  "step-10-casting|Casting and DTO|166689a"
)

branch="main"
tmp="${branch}-build-$$"

git checkout --orphan "$tmp"

if [[ -n "$skeleton_dir" && -d "$skeleton_dir" ]]; then
  rsync -a --delete --exclude .git "$skeleton_dir"/ .
  git add -A
  git commit -m "Initial commit"
else
  echo "Usage: $0 /path/to/skeleton-tree" >&2
  echo "Skeleton must contain core-2 + stack only (no HTTP route yet)." >&2
  exit 1
fi

for entry in "${steps[@]}"; do
  IFS='|' read -r tag title commit <<< "$entry"
  echo "→ $tag ($commit)"
  git read-tree --reset -u "$commit"
  git checkout "${AGENTS_REF:-main}" -- agents/ 2>/dev/null || true
  git add -A
  git commit -m "$title ($tag)"
  git tag -f "$tag"
done

git tag -f web/step-02-view step-02-view
git tag -f web/step-05-session step-05-session
git tag -f api/step-04-json-api step-04-json-api

git branch -M "$branch"
echo ""
echo "Done. Log:"
git log --oneline "$branch"
