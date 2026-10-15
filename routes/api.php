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
