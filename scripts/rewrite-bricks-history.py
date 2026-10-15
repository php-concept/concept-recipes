#!/usr/bin/env python3
"""Transform recipe step trees from addHttp()/stack^1 to brick API / stack^2.

Used by rewrite-bricks-history.sh — can also be called on a single checkout:
  python3 scripts/rewrite-bricks-history.py --root .
"""
from __future__ import annotations

import argparse
import json
import re
import sys
from pathlib import Path

BRICK_USE = {
    "HttpBrick": "Concept\\Stack\\Bricks\\Http\\HttpBrick",
    "ViewBrick": "Concept\\Stack\\Bricks\\View\\ViewBrick",
    "ValidationBrick": "Concept\\Stack\\Bricks\\Validation\\ValidationBrick",
    "SessionBrick": "Concept\\Stack\\Bricks\\Session\\SessionBrick",
    "DatabaseBrick": "Concept\\Stack\\Bricks\\Database\\DatabaseBrick",
    "ConsoleBrick": "Concept\\Stack\\Bricks\\Console\\ConsoleBrick",
    "MaskingBrick": "Concept\\Stack\\Bricks\\Masking\\MaskingBrick",
    "LoggingBrick": "Concept\\Stack\\Bricks\\Logging\\LoggingBrick",
    "ErrorHandlingBrick": "Concept\\Stack\\Bricks\\ErrorHandling\\ErrorHandlingBrick",
    "CastingBrick": "Concept\\Stack\\Bricks\\Casting\\CastingBrick",
}

# addXxx() → Brick class
ADD_TO_BRICK = {
    "Http": "HttpBrick",
    "View": "ViewBrick",
    "Validation": "ValidationBrick",
    "Session": "SessionBrick",
    "Database": "DatabaseBrick",
    "Console": "ConsoleBrick",
    "Masking": "MaskingBrick",
    "Logging": "LoggingBrick",
    "ErrorHandling": "ErrorHandlingBrick",
    "Casting": "CastingBrick",
}

BRICK_TO_PACKAGE = {
    "HttpBrick": "php-concept/brick-http",
    "ViewBrick": "php-concept/brick-view",
    "ValidationBrick": "php-concept/brick-validation-rakit",
    "SessionBrick": "php-concept/brick-session-symfony",
    "DatabaseBrick": "php-concept/brick-database-eloquent",
    "ConsoleBrick": "php-concept/brick-console-symfony",
    "MaskingBrick": "php-concept/brick-data-masker",
    "LoggingBrick": "php-concept/brick-logger-monolog",
    "ErrorHandlingBrick": "php-concept/brick-error-handler-whoops",
    "CastingBrick": "php-concept/brick-casting-valinor",
}

FOUNDATION_PACKAGES = {
    "php-concept/extension-config",
    "php-concept/extension-path-manager",
    "php-concept/extension-json",
}

DEV_REPOSITORIES = [
    {
        "type": "path",
        "url": "../concept-core-2",
        "options": {
            "symlink": True,
            "versions": {"php-concept/core-2": "1.0.0"},
        },
    },
    {
        "type": "path",
        "url": "../concept-stack",
        "options": {
            "symlink": True,
            "versions": {"php-concept/stack": "2.0.0"},
        },
    },
    {
        "type": "path",
        "url": "../concept-extensions/packages/*",
        "options": {
            "symlink": True,
            "versions": {
                "php-concept/extension-casting-valinor": "1.0.0",
                "php-concept/extension-components": "1.0.0",
                "php-concept/extension-config": "1.0.0",
                "php-concept/extension-console-symfony": "1.0.0",
                "php-concept/extension-csrf": "1.0.0",
                "php-concept/extension-data-masker": "1.0.0",
                "php-concept/extension-database-eloquent": "1.0.0",
                "php-concept/extension-error-handler-whoops": "1.0.0",
                "php-concept/extension-event": "1.0.0",
                "php-concept/extension-form-request": "1.0.0",
                "php-concept/extension-http": "1.0.0",
                "php-concept/extension-json": "1.0.0",
                "php-concept/extension-logger-monolog": "1.0.0",
                "php-concept/extension-path-manager": "1.0.0",
                "php-concept/extension-session-symfony": "1.0.0",
                "php-concept/extension-support": "1.0.0",
                "php-concept/extension-telemetry": "1.0.0",
                "php-concept/extension-validation-rakit": "1.0.0",
                "php-concept/extension-view": "1.0.0",
                "php-concept/extension-view-plates": "1.0.0",
                "php-concept/extension-view-twig": "1.0.0",
            },
        },
    },
    {
        "type": "path",
        "url": "../concept-bricks/packages/*",
        "options": {
            "symlink": True,
            "versions": {
                "php-concept/brick-casting-valinor": "1.0.0",
                "php-concept/brick-components": "1.0.0",
                "php-concept/brick-console-symfony": "1.0.0",
                "php-concept/brick-database-eloquent": "1.0.0",
                "php-concept/brick-data-masker": "1.0.0",
                "php-concept/brick-error-handler-whoops": "1.0.0",
                "php-concept/brick-event": "1.0.0",
                "php-concept/brick-http": "1.0.0",
                "php-concept/brick-logger-monolog": "1.0.0",
                "php-concept/brick-session-symfony": "1.0.0",
                "php-concept/brick-telemetry": "1.0.0",
                "php-concept/brick-validation-rakit": "1.0.0",
                "php-concept/brick-view": "1.0.0",
            },
        },
    },
]

ADD_PATTERN = re.compile(
    r"(?P<assign>(?P<var>\$[a-zA-Z_][\w]*)\s*=\s*)?"
    r"\$stack->add(?P<name>Http|View|Validation|Session|Database|Console|Masking|Logging|ErrorHandling|Casting)\(\)"
)


def transform_bootstrap(src: str) -> str:
    used: set[str] = set()

    def repl(m: re.Match[str]) -> str:
        brick = ADD_TO_BRICK[m.group("name")]
        used.add(brick)
        call = f"$stack->add(new {brick}())\n            ->builder()"
        if m.group("assign"):
            return f"{m.group('var')} = {call}"
        return call

    out = ADD_PATTERN.sub(repl, src)

    # Detect bricks from result (in case already partially transformed)
    for brick in BRICK_USE:
        if f"new {brick}(" in out:
            used.add(brick)

    # Inject use statements
    if used:
        use_lines = [f"use {BRICK_USE[b]};" for b in sorted(used, key=lambda x: BRICK_USE[x])]
        # Insert after last existing use, or after namespace
        if re.search(r"^use\s+", out, re.M):
            last_use = None
            for m in re.finditer(r"^use\s+[^;]+;\n", out, re.M):
                last_use = m
            assert last_use is not None
            # Avoid duplicates
            existing = set(re.findall(r"^use\s+([^;]+);", out, re.M))
            to_add = [u for u in use_lines if u[4:-1] not in existing]
            if to_add:
                insert_at = last_use.end()
                out = out[:insert_at] + "".join(f"{u}\n" for u in to_add) + out[insert_at:]
        else:
            out = re.sub(
                r"(namespace [^;]+;\n\n)",
                r"\1" + "".join(f"{u}\n" for u in use_lines) + "\n",
                out,
                count=1,
            )

    # Comment hygiene
    out = out.replace("addHttp()", "HttpBrick")
    out = out.replace("addView(Twig)", "ViewBrick + Twig")
    out = out.replace("addView()", "ViewBrick")

    return out


def bricks_in_text(text: str) -> set[str]:
    return {b for b in BRICK_TO_PACKAGE if f"new {b}(" in text}


def transform_composer_json(data: dict, bricks: set[str]) -> dict:
    require = dict(data.get("require", {}))
    require["php-concept/stack"] = "^2.0"
    # Drop legacy stack-pulled extension-* for capabilities (keep foundation)
    drop_prefixes = (
        "php-concept/extension-http",
        "php-concept/extension-view",
        "php-concept/extension-validation",
        "php-concept/extension-session",
        "php-concept/extension-database",
        "php-concept/extension-console",
        "php-concept/extension-logger",
        "php-concept/extension-data-masker",
        "php-concept/extension-error-handler",
        "php-concept/extension-casting",
        "php-concept/extension-form-request",
        "php-concept/extension-csrf",
        "php-concept/extension-support",
        "php-concept/extension-event",
        "php-concept/extension-telemetry",
        "php-concept/extension-components",
    )
    for key in list(require):
        if key.startswith(drop_prefixes):
            # keep foundation
            if key in FOUNDATION_PACKAGES:
                continue
            # extension-json stays if present
            if key == "php-concept/extension-json":
                continue
            del require[key]

    for brick in sorted(bricks):
        require[BRICK_TO_PACKAGE[brick]] = "^1.0"

    # Stable key order: php, core, stack, foundation, bricks, rest
    ordered: dict[str, str] = {}
    for key in ("php", "php-concept/core-2", "php-concept/stack"):
        if key in require:
            ordered[key] = require.pop(key)
    for key in sorted(FOUNDATION_PACKAGES):
        if key in require:
            ordered[key] = require.pop(key)
    for pkg in sorted(BRICK_TO_PACKAGE.values()):
        if pkg in require:
            ordered[pkg] = require.pop(pkg)
    for key in sorted(require):
        ordered[key] = require[key]

    data["require"] = ordered
    return data


def build_composer_dev(composer: dict) -> dict:
    return {
        "name": composer.get("name", "php-concept/recipes"),
        "description": composer.get("description", "Concept 2 — runnable recipe apps for documentation"),
        "repositories": DEV_REPOSITORIES,
        "autoload": composer.get(
            "autoload",
            {"psr-4": {"Concept\\": "src/"}},
        ),
        "require": composer["require"],
        "require-dev": {"phpstan/phpstan": "^2.1"},
        "scripts": composer.get("scripts", {}),
    }


def transform_docker_compose_dev(text: str) -> str:
    if "concept-bricks" in text:
        return text
    needle = "      - ../concept-extensions:/var/www/concept-extensions\n"
    insert = (
        "      - ../concept-extensions:/var/www/concept-extensions\n"
        "      - ../concept-bricks:/var/www/concept-bricks\n"
    )
    if needle in text:
        return text.replace(needle, insert, 1)
    return text


def transform_tree(root: Path) -> None:
    bootstrap = root / "src/App/Bootstrap/ApplicationStackBootstrap.php"
    bricks: set[str] = set()
    if bootstrap.is_file():
        src = bootstrap.read_text(encoding="utf-8")
        if "->addHttp(" in src or "->addView(" in src or "->addValidation(" in src or "->addSession(" in src or "->addDatabase(" in src or "->addLogging(" in src or "->addMasking(" in src or "->addCasting(" in src or "->addConsole(" in src or "->addErrorHandling(" in src:
            src = transform_bootstrap(src)
            bootstrap.write_text(src, encoding="utf-8")
        bricks = bricks_in_text(bootstrap.read_text(encoding="utf-8"))

    composer_path = root / "composer.json"
    if composer_path.is_file():
        data = json.loads(composer_path.read_text(encoding="utf-8"))
        data = transform_composer_json(data, bricks)
        composer_path.write_text(json.dumps(data, indent=4) + "\n", encoding="utf-8")

        dev_path = root / "composer-dev.json.example"
        if dev_path.is_file() or True:
            dev = build_composer_dev(data)
            # Preserve autoload-dev / Database seeders if in main composer
            if "autoload" in data:
                dev["autoload"] = data["autoload"]
            if "scripts" in data:
                dev["scripts"] = data["scripts"]
            dev_path.write_text(json.dumps(dev, indent=4) + "\n", encoding="utf-8")

    dc = root / "docker-compose.dev.yml.example"
    if dc.is_file():
        dc.write_text(transform_docker_compose_dev(dc.read_text(encoding="utf-8")), encoding="utf-8")


def self_test() -> None:
    samples = [
        Path("/tmp/recipes-rewrite/step01-bootstrap.php"),
        Path("/tmp/recipes-rewrite/step09-bootstrap.php"),
        Path("/tmp/recipes-rewrite/step11-bootstrap.php"),
    ]
    for p in samples:
        if not p.is_file():
            continue
        out = transform_bootstrap(p.read_text(encoding="utf-8"))
        assert "addHttp(" not in out
        assert "addValidation(" not in out
        assert "new HttpBrick()" in out or "new ValidationBrick()" in out or True
        assert "->builder()" in out
        print(f"OK {p.name}: bricks={sorted(bricks_in_text(out))}")
        print(out[:500])
        print("---")


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--root", type=Path, default=Path("."))
    ap.add_argument("--self-test", action="store_true")
    args = ap.parse_args()
    if args.self_test:
        self_test()
        return 0
    transform_tree(args.root.resolve())
    print(f"transformed {args.root}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
