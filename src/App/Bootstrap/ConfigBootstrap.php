<?php declare(strict_types=1);

namespace Concept\App\Bootstrap;

use Concept\Extensions\Config\ConfigServiceProvider;
use League\Container\Container;

/**
 * Registers Config from config/*.php and .env (PathManager — step 12).
 */
final class ConfigBootstrap
{
    public function __construct(
        private readonly string $root,
    ) {}

    public function boot(Container $container): void
    {
        $container->addServiceProvider(new ConfigServiceProvider(
            root: $this->root,
            configDirectory: $this->root . '/config',
        ));
    }
}
