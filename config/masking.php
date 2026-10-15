<?php declare(strict_types=1);

return [
    'masking' => [
        'patterns' => [
            '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.[a-z]{2,}/i' => '***@***.***',
        ],
        'key_patterns' => [
            '/^email$/i',
            '/.*password.*/i',
            '/.*token.*/i',
            '/.*secret.*/i',
        ],
    ],
];
