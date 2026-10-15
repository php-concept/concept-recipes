<?php declare(strict_types=1);

namespace Concept\App\Bootstrap;

use Concept\Stack\ConceptStack;
use League\Container\Container;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Concept\Stack\Bricks\Http\HttpBrick;
use Concept\Stack\Bricks\Session\SessionBrick;
use Concept\Stack\Bricks\Validation\ValidationBrick;
use Concept\Stack\Bricks\View\ViewBrick;

/**
 * Stack recipe: web track — Http + View(Twig) + Validation + Session/CSRF.
 */
final class ApplicationStackBootstrap
{
    public function boot(Container $container): void
    {
        $rootPath = __DIR__ . '/../../../';
        $stack = ConceptStack::create();

        $stack->add(new ValidationBrick())
            ->builder();

        $stack->add(new SessionBrick())
            ->builder()
            ->setHandlerFactory(function() use ($rootPath) {
                return new NativeFileSessionHandler($rootPath . 'storage/sessions');
            })
            ->withCsrf();

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
