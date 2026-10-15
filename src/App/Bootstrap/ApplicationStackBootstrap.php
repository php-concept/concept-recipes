<?php declare(strict_types=1);

namespace Concept\App\Bootstrap;

use Concept\Stack\ConceptStack;
use League\Container\Container;
use Concept\Stack\Bricks\Http\HttpBrick;

/**
 * Stack recipe: web track, step 01 — HttpBrick only.
 */
final class ApplicationStackBootstrap
{
    public function boot(Container $container): void
    {
        $rootPath = __DIR__ . '/../../../';
        $stack = ConceptStack::create();

        $stack->add(new HttpBrick())
            ->builder()
            ->setRoutes([$rootPath . 'routes/web.php']);

        foreach ($stack->providers() as $provider) {
            $container->addServiceProvider($provider);
        }
    }
}
