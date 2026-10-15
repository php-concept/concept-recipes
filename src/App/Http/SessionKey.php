<?php declare(strict_types=1);

namespace Concept\App\Http;

/**
 * App session storage keys (recipe glue, not extension contracts).
 */
final class SessionKey
{
    public const string URL_PREVIOUS = '_url_previous';
    public const string URL_CURRENT = '_url_current';
}
