#!/usr/bin/env bash
# Rebase helper: amend step-05 (SessionKey URL) and step-07 (validation + ShareViewData constants).
set -euo pipefail
ROOT="$(git rev-parse --show-toplevel)"
cd "$ROOT"
MSG=$(git log -1 --format=%s)

case "$MSG" in
  *step-05-session*)
    cat > src/App/Http/SessionKey.php <<'PHP'
<?php declare(strict_types=1);

namespace Concept\App\Http;

/**
 * App session storage keys (recipe glue, not extension contracts).
 */
final class SessionKey
{
    public const string URL_PREVIOUS = '_url_previous';
    public const string URL_CURRENT = '_url_current';
}
PHP
    cat > src/App/Middleware/StorePreviousUrlMiddleware.php <<'PHP'
<?php declare(strict_types=1);

namespace Concept\App\Middleware;

use Concept\App\Http\SessionKey;
use Concept\Extensions\Http\Protocol\HttpHeader;
use Concept\Extensions\Http\Protocol\HttpMethod;
use Concept\Extensions\Http\Protocol\HttpValue;
use Concept\Extensions\Http\Requests\RequestAttribute;
use Concept\Extensions\SessionSymfony\Contracts\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Remembers the last GET URL so redirectBack() can return to the form after CSRF failure.
 */
final class StorePreviousUrlMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SessionInterface $session,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getMethod();
        $uri = (string) $request->getUri();

        if ($method === HttpMethod::GET && !$this->isAjax($request)) {
            $currentInSession = $this->session->get(SessionKey::URL_CURRENT);

            if ($uri !== $currentInSession) {
                $this->session->set(SessionKey::URL_PREVIOUS, $currentInSession);
                $this->session->set(SessionKey::URL_CURRENT, $uri);
            }
        }

        $backUrl = $method === HttpMethod::GET
            ? $this->session->get(SessionKey::URL_PREVIOUS)
            : $this->session->get(SessionKey::URL_CURRENT);

        return $handler->handle(
            $request->withAttribute(RequestAttribute::SAFE_BACK_URL, $backUrl),
        );
    }

    private function isAjax(ServerRequestInterface $request): bool
    {
        return $request->getHeaderLine(HttpHeader::X_REQUESTED_WITH) === HttpValue::XML_HTTP_REQUEST;
    }
}
PHP
    git add src/App/Http/SessionKey.php src/App/Middleware/StorePreviousUrlMiddleware.php
    git commit --amend --no-edit
    ;;
  *step-07-error-handling*)
    cat > src/App/Http/SessionKey.php <<'PHP'
<?php declare(strict_types=1);

namespace Concept\App\Http;

/**
 * App session / flash storage keys (recipe glue, not extension contracts).
 */
final class SessionKey
{
    public const string VALIDATION_ERRORS = '_validation_errors';
    public const string VALIDATION_DATA = '_validation_data';
    public const string URL_PREVIOUS = '_url_previous';
    public const string URL_CURRENT = '_url_current';
}
PHP
    cat > src/App/Middleware/ShareViewDataMiddleware.php <<'PHP'
<?php declare(strict_types=1);

namespace Concept\App\Middleware;

use Concept\App\Http\SessionKey;
use Concept\Extensions\Csrf\Contracts\CsrfTokenManagerInterface;
use Concept\Extensions\View\Requests\RequestAttribute;
use Concept\Extensions\SessionSymfony\Contracts\FlashBagInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Puts csrf_token, flashes, validation errors/old into VIEW_PAYLOAD for ViewResponseFactory.
 */
final class ShareViewDataMiddleware implements MiddlewareInterface
{
    private const string TEMPLATE_ERRORS = 'errors';
    private const string TEMPLATE_OLD_INPUT = 'old';
    private const string TEMPLATE_FLASHES = 'flashes';
    private const string TEMPLATE_CSRF_TOKEN = 'csrf_token';

    public function __construct(
        private readonly FlashBagInterface $flashBag,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request->withAttribute(RequestAttribute::VIEW_PAYLOAD, [
            self::TEMPLATE_ERRORS => $this->flashBag->get(SessionKey::VALIDATION_ERRORS, []),
            self::TEMPLATE_OLD_INPUT => $this->flashBag->get(SessionKey::VALIDATION_DATA, []),
            self::TEMPLATE_FLASHES => $this->flashBag->all(),
            self::TEMPLATE_CSRF_TOKEN => $this->csrfTokenManager->getToken(),
        ]));
    }
}
PHP
    git add src/App/Http/SessionKey.php src/App/Middleware/ShareViewDataMiddleware.php
    git commit --amend --no-edit
    ;;
esac
