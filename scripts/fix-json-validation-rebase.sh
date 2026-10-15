#!/usr/bin/env bash
# Rebase helper: step-07 (JSON validation middleware + API type-hint), step-10 (ApiController + toDto).
set -euo pipefail
ROOT="$(git rev-parse --show-toplevel)"
cd "$ROOT"
MSG=$(git log -1 --format=%s)

write_json_validation_middleware() {
    cat > src/App/Middleware/HandleJsonValidationExceptionMiddleware.php <<'PHP'
<?php declare(strict_types=1);

namespace Concept\App\Middleware;

use Concept\Extensions\Http\Contracts\ResponseFactoryInterface;
use Concept\Extensions\Http\Protocol\HttpStatusCode;
use Concept\Extensions\ValidationRakit\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** API: validation errors → always 422 JSON (no session / flash). */
final class HandleJsonValidationExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            return $this->responseFactory->jsonError(
                $e->getMessage(),
                HttpStatusCode::UNPROCESSABLE_ENTITY,
                $e->getErrors(),
            );
        }
    }
}
PHP
}

write_api_routes() {
    cat > routes/api.php <<'PHP'
<?php declare(strict_types=1);

use Concept\App\Controllers\ApiController;
use Concept\App\Middleware\HandleJsonValidationExceptionMiddleware;
use Concept\Extensions\Json\Middleware\ForceJsonResponseMiddleware;
use Concept\Extensions\Json\Middleware\ParseJsonBodyMiddleware;
use League\Route\RouteGroup;
use League\Route\Router;

/** @var Router $router */
$router->group('/api', function (RouteGroup $router): void {
    $router->get('/ping', [ApiController::class, 'ping'])->setName('api.ping');
    $router->get('/stats', [ApiController::class, 'stats'])->setName('api.stats');
    $router->post('/contact', [ApiController::class, 'contact'])->setName('api.contact');
})->lazyMiddlewares([
    ParseJsonBodyMiddleware::class,
    ForceJsonResponseMiddleware::class,
    HandleJsonValidationExceptionMiddleware::class,
]);
PHP
}

write_api_controller_step7() {
    cat > src/App/Controllers/ApiController.php <<'PHP'
<?php declare(strict_types=1);

namespace Concept\App\Controllers;

use Concept\App\Http\Requests\ContactFormRequest;
use Concept\Extensions\DatabaseEloquent\Contracts\DatabaseInterface;
use Concept\Extensions\Http\Contracts\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class ApiController
{
    public function __construct(
        private readonly ResponseFactoryInterface $response,
        private readonly DatabaseInterface $database,
    ) {}

    public function ping(): ResponseInterface
    {
        return $this->response->jsonSuccess(['message' => 'pong']);
    }

    public function stats(): ResponseInterface
    {
        $submissions = $this->database->capsule()->table('contact_submissions');

        return $this->response->jsonSuccess([
            'connection' => 'ok',
            'contact_submissions_total' => $submissions->count(),
        ]);
    }

    /**
     * Type-hint FormRequest: resolver validates or throws ValidationException
     * (HandleJsonValidationExceptionMiddleware → 422 JSON).
     */
    public function contact(ContactFormRequest $form): ResponseInterface
    {
        $validated = $form->validated();

        $this->database->capsule()->table('contact_submissions')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->jsonSuccess([
            'validated' => $validated,
            'saved' => true,
        ]);
    }
}
PHP
}

write_api_controller_step10() {
    cat > src/App/Controllers/ApiController.php <<'PHP'
<?php declare(strict_types=1);

namespace Concept\App\Controllers;

use Concept\App\Dto\ContactDto;
use Concept\App\Http\Requests\ContactFormRequest;
use Concept\Extensions\DatabaseEloquent\Contracts\DatabaseInterface;
use Concept\Extensions\Http\Contracts\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class ApiController
{
    public function __construct(
        private readonly ResponseFactoryInterface $response,
        private readonly DatabaseInterface $database,
    ) {}

    public function ping(): ResponseInterface
    {
        return $this->response->jsonSuccess(['message' => 'pong']);
    }

    public function stats(): ResponseInterface
    {
        $submissions = $this->database->capsule()->table('contact_submissions');

        return $this->response->jsonSuccess([
            'connection' => 'ok',
            'contact_submissions_total' => $submissions->count(),
        ]);
    }

    /**
     * Type-hint FormRequest: resolver validates or throws ValidationException
     * (HandleJsonValidationExceptionMiddleware → 422 JSON). After casting: toDto().
     */
    public function contact(ContactFormRequest $form): ResponseInterface
    {
        $dto = $form->toDto();
        if (!$dto instanceof ContactDto) {
            throw new \RuntimeException('ContactDto casting failed — is addCasting() enabled?');
        }

        $this->database->capsule()->table('contact_submissions')->insert($dto->toArray());

        return $this->response->jsonSuccess([
            'validated' => $dto->toArray(),
            'saved' => true,
        ]);
    }
}
PHP
}

case "$MSG" in
  *step-07-error-handling*)
    write_json_validation_middleware
    write_api_routes
    write_api_controller_step7
    git add src/App/Middleware/HandleJsonValidationExceptionMiddleware.php routes/api.php src/App/Controllers/ApiController.php
    git commit --amend --no-edit
    ;;
  *step-10-casting*)
    write_api_controller_step10
    git add src/App/Controllers/ApiController.php
    git commit --amend --no-edit
    ;;
esac
