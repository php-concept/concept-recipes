<?php declare(strict_types=1);

namespace Concept\App\Bootstrap;

use Concept\App\Foundation\AppConfig;
use Concept\App\Foundation\ConfigKey;
use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\Config\Contracts\ConfigInterface;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbMigrateCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbMigrationListCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbRollbackCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbSeedCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbSeederListCommand;
use Concept\Stack\ConceptStack;
use League\Container\Container;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Whoops\Handler\PrettyPageHandler;
use Concept\Stack\Bricks\Casting\CastingBrick;
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
 * Stack recipe: Config — values from config/*.php + .env; paths still literal until step 12.
 */
final class ApplicationStackBootstrap
{
    public function boot(Container $container): void
    {
        $rootPath = __DIR__ . '/../../../';
        $config = ContainerDependency::get($container, ConfigInterface::class);
        $app = new AppConfig($config);
        $debug = $config->getBool(ConfigKey::APP_DEBUG);

        $stack = ConceptStack::create();

        $stack->add(new CastingBrick())
            ->builder()
            ->setCacheDir($rootPath . 'storage/cache/casting')
            ->setDebug($debug);

        $validation = $stack->add(new ValidationBrick())
            ->builder();
        if ($config->getBool(ConfigKey::VALIDATOR_LOG_ENABLED)) {
            $validation->withLogging(
                $rootPath . 'storage/logs/' . $config->getString(ConfigKey::VALIDATOR_LOG_FILE, 'validation.log'),
                $config->getInt(ConfigKey::VALIDATOR_LOG_MAX_FILES, 7),
            );
        }
        $validation->withMasking();

        $database = $stack->add(new DatabaseBrick())
            ->builder()
            ->setConnection($app->dbConnection())
            ->setMigrations([$rootPath . 'database/migrations'])
            ->setMigrationsTable($config->getString(ConfigKey::MIGRATIONS_TABLE, 'migrations'))
            ->setSeeders($app->seeders())
            ->withMasking();

        if ($config->getBool(ConfigKey::DB_LOG_ENABLED)) {
            $database->withQueryLogging(
                $rootPath . 'storage/logs/' . $config->getString(ConfigKey::DB_LOG_FILE, 'query.log'),
                $config->getInt(ConfigKey::DB_LOG_MAX_FILES, 7),
            );
        }

        $stack->add(new SessionBrick())
            ->builder()
            ->setHandlerFactory(function () use ($rootPath) {
                return new NativeFileSessionHandler($rootPath . 'storage/sessions');
            })
            ->withCsrf();

        $stack->add(new HttpBrick())
            ->builder()
            ->setRoutes([
                $rootPath . 'routes/web.php',
                $rootPath . 'routes/api.php',
            ])
            ->withFormRequests()
            ->withTypedRouteParameters();

        $stack->add(new ViewBrick())
            ->builder()
            ->withTwig()
            ->setViewsPath($rootPath . 'resources/views')
            ->setCacheDir($rootPath . 'storage/cache/views')
            ->setDebug($debug);

        $stack->add(new ConsoleBrick())
            ->builder()
            ->setName($config->getString(ConfigKey::APP_NAME, 'Recipes'))
            ->setVersion($config->getString(ConfigKey::APP_VERSION, '1.0.0'))
            ->setCommands([
                DbMigrateCommand::class,
                DbRollbackCommand::class,
                DbMigrationListCommand::class,
                DbSeedCommand::class,
                DbSeederListCommand::class,
            ]);

        $stack->add(new MaskingBrick())
            ->builder()
            ->setPatterns($app->maskingPatterns())
            ->setKeyPatterns($app->maskingKeyPatterns());

        $appLogFile = $config->getString(ConfigKey::LOG_FILE, 'app.log');
        $stack->add(new LoggingBrick())
            ->builder()
            ->setLevel($config->getString(ConfigKey::LOG_LEVEL, 'INFO'))
            ->setChannel($config->getString(ConfigKey::LOG_NAME, 'recipes'))
            ->toRotatingFile($rootPath . 'storage/logs/' . $appLogFile, $config->getInt(ConfigKey::LOG_MAX_FILES, 7))
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
