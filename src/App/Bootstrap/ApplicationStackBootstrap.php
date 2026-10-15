<?php declare(strict_types=1);

namespace Concept\App\Bootstrap;

use Concept\Stack\ConceptStack;
use League\Container\Container;
use Concept\Stack\Bricks\Http\HttpBrick;
use Concept\Stack\Bricks\View\ViewBrick;

/**
 * Stack recipe: web track — HttpBrick + ViewBrick + Twig.
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

        $stack->add(new ViewBrick())
            ->builder()
            ->withTwig()
            ->setViewsPath($rootPath . 'resources/views');

        foreach ($stack->providers() as $provider) {
            $container->addServiceProvider($provider);
        }
    }
}
