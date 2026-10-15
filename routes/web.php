<?php declare(strict_types=1);

use Concept\App\Controllers\ContactController;
use Concept\App\Controllers\IndexController;
use Concept\App\Middleware\StorePreviousUrlMiddleware;
use Concept\Extensions\Csrf\Middleware\HandleCsrfExceptionMiddleware;
use Concept\Extensions\Csrf\Middleware\VerifyCsrfTokenMiddleware;
use League\Route\Router;

/** @var Router $router */
$router->lazyMiddlewares([
    StorePreviousUrlMiddleware::class,
    HandleCsrfExceptionMiddleware::class,
    VerifyCsrfTokenMiddleware::class,
]);

$router->get('/', [IndexController::class, 'index'])->setName('home');

$router->get('/contact', [ContactController::class, 'show'])->setName('contact');
$router->post('/contact', [ContactController::class, 'submit'])->setName('contact.submit');
