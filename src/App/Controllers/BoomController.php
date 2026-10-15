<?php declare(strict_types=1);

namespace Concept\App\Controllers;

use RuntimeException;

/**
 * Demo endpoint for ErrorHandling / Whoops (recipe step 7).
 */
final class BoomController
{
    public function __invoke(): never
    {
        throw new RuntimeException('Recipe boom — Whoops (APP_DEBUG=1) or fallback HTML (APP_DEBUG=0).');
    }
}
