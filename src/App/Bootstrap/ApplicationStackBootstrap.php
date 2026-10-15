<?php declare(strict_types=1);

namespace Concept\App\Bootstrap;

use Concept\Extensions\DatabaseEloquent\Console\Commands\DbMigrateCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbMigrationListCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbRollbackCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbSeedCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbSeederListCommand;
use Concept\Stack\ConceptStack;
use Database\Seeders\ContactSubmissionsSeeder;
use League\Container\Container;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Concept\Stack\Bricks\Console\ConsoleBrick;
use Concept\Stack\Bricks\Database\DatabaseBrick;
use Concept\Stack\Bricks\Http\HttpBrick;
use Concept\Stack\Bricks\Session\SessionBrick;
use Concept\Stack\Bricks\Validation\ValidationBrick;
use Concept\Stack\Bricks\View\ViewBrick;

/**
 * Stack recipe: merged web + API — Http, View, Validation, Session/CSRF, Database, Console.
 */
final class ApplicationStackBootstrap
{
    public function boot(Container $container): void
    {
        $rootPath = __DIR__ . '/../../../';
        $stack = ConceptStack::create();

        $stack->add(new ValidationBrick())
            ->builder();

        $stack->add(new DatabaseBrick())
            ->builder()
            ->setConnection([
                'driver' => 'mysql',
                'host' => getenv('MYSQL_HOST') ?: 'concept_recipes_db',
                'port' => (int) (getenv('MYSQL_PORT') ?: 3306),
                'database' => getenv('MYSQL_DATABASE') ?: 'concept_recipes',
                'username' => getenv('MYSQL_USER') ?: 'root',
                'password' => getenv('MYSQL_PASSWORD') ?: 'root',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ])
            ->setMigrations([$rootPath . 'database/migrations'])
            ->setMigrationsTable('migrations')
            ->setSeeders([
                ContactSubmissionsSeeder::class,
            ]);

        $stack->add(new SessionBrick())
            ->builder()
            ->setHandlerFactory(function() use ($rootPath) {
                return new NativeFileSessionHandler($rootPath . 'storage/sessions');
            })
            ->withCsrf();

        $stack->add(new HttpBrick())
            ->builder()
            ->setRoutes([
                $rootPath . 'routes/web.php',
                $rootPath . 'routes/api.php',
            ]);

        $stack->add(new ViewBrick())
            ->builder()
            ->withTwig()
            ->setViewsPath($rootPath . 'resources/views');

        $stack->add(new ConsoleBrick())
            ->builder()
            ->setName('Recipes')
            ->setVersion('1.0.0')
            ->setCommands([
                DbMigrateCommand::class,
                DbRollbackCommand::class,
                DbMigrationListCommand::class,
                DbSeedCommand::class,
                DbSeederListCommand::class,
            ]);

        foreach ($stack->providers() as $provider) {
            $container->addServiceProvider($provider);
        }
    }
}
