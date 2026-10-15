<?php declare(strict_types=1);

use Concept\App\Foundation\PathName;

/**
 * Named paths for this recipe app (subset of skeleton; grow as needed).
 *
 * @return array<string, string>
 */
return [
    PathName::CONFIG => 'config',
    PathName::MIGRATIONS => 'database/migrations',
    PathName::ROUTES => 'routes',
    PathName::VIEWS => 'resources/views',
    PathName::ERRORS_FALLBACK_VIEWS => 'resources/views/errors/fallback',
    PathName::STORAGE => 'storage',
    PathName::LOGS => 'storage/logs',
    PathName::CACHE => 'storage/cache',
];
