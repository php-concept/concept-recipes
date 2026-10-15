<?php declare(strict_types=1);

use Database\Seeders\ContactSubmissionsSeeder;

return [
    'db' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'concept_recipes',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'log_enabled' => true,
        'log_file' => 'query.log',
        'log_max_files' => 7,
    ],
    'migrations' => [
        'table' => 'migrations',
    ],
    'seeders' => [
        'list' => [
            ContactSubmissionsSeeder::class,
        ],
    ],
];
