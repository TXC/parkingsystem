<?php

use TXC\Box\Infrastructure\Environment\Settings;

return [
    'authentication' => [
        'header' => $_ENV['API_HEADER'] ?? 'api-key',
    ],
    'passes' => [
        'console' => [],
        'domain' => [],
        'middleware' => [
            \TXC\Box\Middlewares\WhoopsMiddleware::class,
            \TXC\Box\Middlewares\CORSMiddleware::class,
            //\TXC\Box\Middlewares\LoginMiddleware::class,
            //\TXC\Box\Middlewares\SessionMiddleware::class,
            //\TXC\Box\Middlewares\RateLimitMiddleware::class,
            \TXC\Box\Middlewares\TrailingSlash::class,
            //\TXC\Box\Middlewares\LanguageMiddleware::class,
        ],
        'repository' => [],
        'route' => [],
    ],
    'site' => [],
    'cors' => [
        'origin' => '*',
    ],
    'slim' => [
        'encoding' => 'UTF-8',
        'locale' => 'en_US',
        'available_locales' => [
            'en_US',
            'sv_SE',
        ],
        'route' => [
            'redirect_to' => 'root',
            'public' => [
                'root',
                'login',
                'apiLogin'
            ],
        ],
    ],
    'application' => [
        /**
         * Rate/Zone Definitions
         * Each key in the zone object is the zone name.
         * The value is the per hour rate for that zone.
         * Values will be rounded to 2 decimals and
         * stored in minimum units (i.e. multiplicated by 100)
         */
        'rate' => [
            'A' => [
                'hour' => 5,
                'day' => 96
            ],
            'B' => [
                'hour' => 7.75,
                'day' => 148
            ],
            'C' => [
                'hour' => 8.9,
                'day' => 170
            ],
            'D' => [
                'hour' => 4.1,
                'day' => 79
            ],
        ],
        /**
         * Ticket settings
         */
        'ticket' => [
            /**
             * Ticket is due after issuing date
             */
            'due' => '30 days',
        ]
    ],
];
