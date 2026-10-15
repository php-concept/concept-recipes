<?php declare(strict_types=1);

namespace Concept\App\Bootstrap;

use Concept\App\Foundation\AppConfig;
use Concept\App\Foundation\ConfigKey;
use Concept\App\Foundation\PathName;
use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\Config\Contracts\ConfigInterface;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbMigrateCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbMigrationListCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbRollbackCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbSeedCommand;
use Concept\Extensions\DatabaseEloquent\Console\Commands\DbSeederListCommand;
use Concept\Extensions\PathManager\PathManager;
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
use Concept\Stack\ConceptStack;
use League\Container\Container;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Whoops\Handler\PrettyPageHandler;

/**
 * Stack recipe: PathManager for paths, Config for values. Requires FoundationBootstrap first.
 */
final class ApplicationStackBootstrap
{
    public function boot(Container $container): void
    {
        $pathManager = ContainerDependency::get($container, PathManager::class);
        $config = ContainerDependency::get($container, ConfigInterface::class);
        $app = new AppConfig($config);
        $debug = $config->getBool(ConfigKey::APP_DEBUG);

        $stack = ConceptStack::create();

        $stack->add(new MaskingBrick())
            ->builder()
            ->setPatterns($app->maskingPatterns())
            ->setKeyPatterns($app->maskingKeyPatterns());

        $stack->add(new CastingBrick())
            ->builder()
            ->setCacheDir($pathManager->get(PathName::CACHE, 'casting'))
            ->setDebug($debug);

        $validation = $stack->add(new ValidationBrick())->builder();
        if ($config->getBool(ConfigKey::VALIDATOR_LOG_ENABLED)) {
            $validation->withLogging(
                $pathManager->get(PathName::LOGS, $config->getString(ConfigKey::VALIDATOR_LOG_FILE, 'validation.log')),
                $config->getInt(ConfigKey::VALIDATOR_LOG_MAX_FILES, 7),
            );
        }
        $validation->withMasking();

        $database = $stack->add(new DatabaseBrick())
            ->builder()
            ->setConnection($app->dbConnection())
            ->setMigrations([$pathManager->get(PathName::MIGRATIONS)])
            ->setMigrationsTable($config->getString(ConfigKey::MIGRATIONS_TABLE, 'migrations'))
            ->setSeeders($app->seeders())
            ->withMasking();

        if ($config->getBool(ConfigKey::DB_LOG_ENABLED)) {
            $database->withQueryLogging(
                $pathManager->get(PathName::LOGS, $config->getString(ConfigKey::DB_LOG_FILE, 'query.log')),
                $config->getInt(ConfigKey::DB_LOG_MAX_FILES, 7),
            );
        }

        $stack->add(new SessionBrick())
            ->builder()
            ->setHandlerFactory(function () use ($pathManager) {
                return new NativeFileSessionHandler($pathManager->get(PathName::STORAGE, 'sessions'));
            })
            ->withCsrf();

        $stack->add(new HttpBrick())
            ->builder()
            ->setRoutes([
                $pathManager->get(PathName::ROUTES, 'web.php'),
                $pathManager->get(PathName::ROUTES, 'api.php'),
            ])
            ->withFormRequests()
            ->withTypedRouteParameters();

        $stack->add(new ViewBrick())
            ->builder()
            ->withTwig()
            ->setViewsPath($pathManager->get(PathName::VIEWS))
            ->setCacheDir($pathManager->get(PathName::CACHE, 'views'))
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

        $appLogFile = $config->getString(ConfigKey::LOG_FILE, 'app.log');
        $stack->add(new LoggingBrick())
            ->builder()
            ->setLevel($config->getString(ConfigKey::LOG_LEVEL, 'INFO'))
            ->setChannel($config->getString(ConfigKey::LOG_NAME, 'recipes'))
            ->toRotatingFile($pathManager->get(PathName::LOGS, $appLogFile), $config->getInt(ConfigKey::LOG_MAX_FILES, 7))
            ->withMasking();

        $stack->add(new ErrorHandlingBrick())
            ->builder()
            ->setDebug($debug, new PrettyPageHandler())
            ->setLogReporting(logger: true, phpErrorLog: true)
            ->withViewRenderer($pathManager->get(PathName::ERRORS_FALLBACK_VIEWS));

        foreach ($stack->providers() as $provider) {
            $container->addServiceProvider($provider);
        }
    }
}
