#!/usr/bin/env bash
# Checkout a recipe step tag and reset runtime storage (Twig cache, sessions, logs).
# Usage: ./scripts/checkout-recipe-step.sh step-03-validation
set -euo pipefail

cd "$(git rev-parse --show-toplevel)"

tag="${1:-}"
if [[ -z "$tag" ]]; then
  echo "Usage: $0 <step-tag>" >&2
  echo "Example: $0 step-03-validation" >&2
  exit 1
fi

git checkout "$tag"
rm -rf storage/cache storage/sessions storage/logs
composer install

echo ""
echo "Checked out $tag. Storage runtime cleared. Next: docker compose up -d --build"
