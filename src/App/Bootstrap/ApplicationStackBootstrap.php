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
use Whoops\Handler\PrettyPageHandler;
use Concept\Stack\Bricks\Console\ConsoleBrick;
use Concept\Stack\Bricks\Database\DatabaseBrick;
use Concept\Stack\Bricks\ErrorHandling\ErrorHandlingBrick;
use Concept\Stack\Bricks\Http\HttpBrick;
use Concept\Stack\Bricks\Logging\LoggingBrick;
use Concept\Stack\Bricks\Masking\MaskingBrick;
use Concept\Stack\Bricks\Session\SessionBrick;
use Concept\Stack\Bricks\Validation\ValidationBrick;
use Concept\Stack\Bricks\View\ViewBrick;

/**
 * Stack recipe: diagnostic sinks — validation.log + query.log (with masking).
 * Config / PathManager — next step.
 */
final class ApplicationStackBootstrap
{
    public function boot(Container $container): void
    {
        $rootPath = __DIR__ . '/../../../';
        $debug = getenv('APP_DEBUG') !== '0';
        $stack = ConceptStack::create();

        $stack->add(new ValidationBrick())
            ->builder()
            ->withLogging($rootPath . 'storage/logs/validation.log', 7)
            ->withMasking();

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
            ])
            ->withQueryLogging($rootPath . 'storage/logs/query.log', 7)
            ->withMasking();

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
            ])
            ->withFormRequests();

        $stack->add(new ViewBrick())
            ->builder()
            ->withTwig()
            ->setViewsPath($rootPath . 'resources/views')
            ->setCacheDir($rootPath . 'storage/cache/views')
            ->setDebug($debug);

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

        $stack->add(new MaskingBrick())
            ->builder()
            ->setPatterns([
                '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.[a-z]{2,}/i' => '***@***.***',
            ])
            ->setKeyPatterns([
                '/^email$/i',
                '/.*password.*/i',
                '/.*token.*/i',
                '/.*secret.*/i',
            ]);

        $stack->add(new LoggingBrick())
            ->builder()
            ->setLevel('INFO')
            ->setChannel('recipes')
            ->toRotatingFile($rootPath . 'storage/logs/app.log', 7)
            ->withMasking();

        $stack->add(new ErrorHandlingBrick())
            ->builder()
            ->setDebug($debug, new PrettyPageHandler())
            ->setLogReporting(logger: true, phpErrorLog: true)
            ->withViewRenderer($rootPath . 'resources/views/errors/fallback');

        foreach ($stack->providers() as $provider) {
            $container->addServiceProvider($provider);
        }
    }
}
