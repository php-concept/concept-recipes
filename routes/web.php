<?php declare(strict_types=1);

use Concept\App\Controllers\BoomController;
use Concept\App\Controllers\ContactController;
use Concept\App\Controllers\IndexController;
use Concept\App\Middleware\HandleValidationExceptionMiddleware;
use Concept\App\Middleware\ShareViewDataMiddleware;
use Concept\App\Middleware\StorePreviousUrlMiddleware;
use Concept\Extensions\Csrf\Middleware\HandleCsrfExceptionMiddleware;
use Concept\Extensions\Csrf\Middleware\VerifyCsrfTokenMiddleware;
use League\Route\RouteGroup;
use League\Route\Router;

/** @var Router $router */
$router->group('', function (RouteGroup $router): void {
    $router->get('/', [IndexController::class, 'index'])->setName('home');
    $router->get('/boom', BoomController::class)->setName('boom');

    $router->get('/contact', [ContactController::class, 'show'])->setName('contact');
    $router->post('/contact', [ContactController::class, 'submit'])->setName('contact.submit');
})->lazyMiddlewares([
    StorePreviousUrlMiddleware::class,
    HandleValidationExceptionMiddleware::class,
    HandleCsrfExceptionMiddleware::class,
    VerifyCsrfTokenMiddleware::class,
    ShareViewDataMiddleware::class,
]);
