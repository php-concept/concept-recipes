<?php declare(strict_types=1);

namespace Concept\App\Bootstrap;

use Concept\Stack\ConceptStack;
use League\Container\Container;

/**
 * Stack recipe — empty skeleton before step 01.
 */
final class ApplicationStackBootstrap
{
    public function boot(Container $container): void
    {
        $stack = ConceptStack::create();

        foreach ($stack->providers() as $provider) {
            $container->addServiceProvider($provider);
        }
    }
}
