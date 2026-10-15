<?php declare(strict_types=1);

use Concept\App\Bootstrap\ApplicationRuntimeBootstrap;
use Concept\App\Bootstrap\ApplicationStackBootstrap;
use Concept\App\Bootstrap\ConfigBootstrap;
use Concept\Core\App;
use League\Container\Container;

$app = App::create();

$root = dirname(__DIR__);

/** @var Container $container */
$container = $app->getContainer();

(new ConfigBootstrap($root))->boot($container);
(new ApplicationStackBootstrap())->boot($container);
(new ApplicationRuntimeBootstrap())->boot($container);

return $app;
