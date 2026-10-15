#!/usr/bin/env bash
# Pin agents/ from main (or AGENTS_REF) onto every commit on main.
set -euo pipefail

cd "$(git rev-parse --show-toplevel)"

agents_ref="${AGENTS_REF:-main}"
pin_dir="$(mktemp -d)"

git archive "$agents_ref" agents | tar -x -C "$pin_dir"
if [[ ! -d "$pin_dir/agents" ]]; then
  echo "agents/ not found at $agents_ref" >&2
  exit 1
fi

cp -a "$pin_dir/agents" /tmp/agents-pin
rm -rf "$pin_dir"

export agents_pin=/tmp/agents-pin

git rebase -i --root --exec 'rm -rf agents && cp -a "$agents_pin" agents && git add agents && if ! git diff --cached --quiet; then git commit --amend --no-edit; fi'

echo "Done. agents/ pinned from $agents_ref on all commits."
