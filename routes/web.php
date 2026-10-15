<?php declare(strict_types=1);

use Concept\App\Controllers\ContactController;
use Concept\App\Controllers\IndexController;
use League\Route\Router;

/** @var Router $router */
$router->get('/', [IndexController::class, 'index'])->setName('home');

$router->get('/contact', [ContactController::class, 'show'])->setName('contact');
$router->post('/contact', [ContactController::class, 'submit'])->setName('contact.submit');
