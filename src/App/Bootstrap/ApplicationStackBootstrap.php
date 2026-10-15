<?php declare(strict_types=1);

namespace Concept\App\Bootstrap;

use Concept\Stack\ConceptStack;
use League\Container\Container;
use Concept\Stack\Bricks\Http\HttpBrick;
use Concept\Stack\Bricks\Validation\ValidationBrick;

/**
 * Stack recipe: api track — Http + Validation (JSON surface, no View).
 */
final class ApplicationStackBootstrap
{
    public function boot(Container $container): void
    {
        $rootPath = __DIR__ . '/../../../';
        $stack = ConceptStack::create();

        $stack->add(new ValidationBrick())
            ->builder();

        $stack->add(new HttpBrick())
            ->builder()
            ->setRoutes([$rootPath . 'routes/api.php']);

        foreach ($stack->providers() as $provider) {
            $container->addServiceProvider($provider);
        }
    }
}
