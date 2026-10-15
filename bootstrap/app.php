<?php declare(strict_types=1);

use Concept\App\Bootstrap\ApplicationRuntimeBootstrap;
use Concept\App\Bootstrap\ApplicationStackBootstrap;
use Concept\App\Bootstrap\FoundationBootstrap;
use Concept\Core\App;
use League\Container\Container;

$app = App::create();

/** @var array<string, string> $pathMap */
$pathMap = require __DIR__ . '/path-map.php';
$root = dirname(__DIR__);

/** @var Container $container */
$container = $app->getContainer();

(new FoundationBootstrap($root, $pathMap))->boot($container);
(new ApplicationStackBootstrap())->boot($container);
(new ApplicationRuntimeBootstrap())->boot($container);

return $app;
