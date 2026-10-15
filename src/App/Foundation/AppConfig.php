<?php declare(strict_types=1);

namespace Concept\App\Foundation;

use Concept\Extensions\Config\Contracts\ConfigInterface;

/**
 * App-specific composite config shapes for Stack bootstrap (glue).
 * Scalars — via ConfigInterface / ConfigKey in bootstrap; not one-line wrappers here.
 */
final class AppConfig
{
    public function __construct(
        private readonly ConfigInterface $config,
    ) {}

    /**
     * @return array{
     *     driver: string,
     *     host: string,
     *     port: int,
     *     database: string,
     *     username: string,
     *     password: string,
     *     charset: string,
     *     collation: string,
     *     prefix: string
     * }
     */
    public function dbConnection(): array
    {
        return [
            'driver' => $this->config->getString(ConfigKey::DB_DRIVER),
            'host' => $this->config->getString(ConfigKey::DB_HOST),
            'port' => $this->config->getInt(ConfigKey::DB_PORT, 3306),
            'database' => $this->config->getString(ConfigKey::DB_DATABASE),
            'username' => $this->config->getString(ConfigKey::DB_USERNAME),
            'password' => $this->config->getString(ConfigKey::DB_PASSWORD),
            'charset' => $this->config->getString(ConfigKey::DB_CHARSET, 'utf8mb4'),
            'collation' => $this->config->getString(ConfigKey::DB_COLLATION, 'utf8mb4_unicode_ci'),
            'prefix' => $this->config->getString(ConfigKey::DB_PREFIX),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function maskingPatterns(): array
    {
        $map = [];
        foreach ($this->config->getArray(ConfigKey::MASKING_PATTERNS) as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $map[$key] = $value;
            }
        }

        return $map;
    }

    /**
     * @return list<string>
     */
    public function maskingKeyPatterns(): array
    {
        $items = [];
        foreach ($this->config->getArray(ConfigKey::MASKING_KEY_PATTERNS) as $item) {
            if (is_string($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @return list<class-string>
     */
    public function seeders(): array
    {
        $items = [];
        foreach ($this->config->getArray(ConfigKey::SEEDERS_LIST) as $item) {
            if (is_string($item) && $item !== '' && class_exists($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
