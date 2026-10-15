<?php declare(strict_types=1);

namespace Concept\App\Dto;

use Concept\Extensions\CastingValinor\Dto\Dto;

/**
 * Typed contact payload after FormRequest validation (recipe step 10).
 */
final class ContactDto extends Dto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $message,
    ) {}
}
