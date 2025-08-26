<?php

return [
    'default' => 'default',

    // --- فرمت جدید (v9+) ---
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Postal Tracking API',
            ],
            'routes' => [
                'api' => 'api/documentation',
            ],
            'paths' => [
                'docs' => storage_path('api-docs'),
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',

                // مسیرهای اسکن (new format)
                'annotations' => [
                    base_path('app/Swagger'),
                ],
            ],
        ],
    ],

    // --- defaults (legacy و مشترک) ---
    'defaults' => [
        'routes' => [
            'middleware' => ['api'],
        ],
        'paths' => [
            // بعضی نسخه‌ها این کلیدها رو از این‌جا می‌خوان
            'docs' => storage_path('api-docs'),
            'views' => base_path('resources/views/vendor/l5-swagger'),
            'base'  => env('L5_SWAGGER_BASE_PATH', null),
            'use_absolute_path' => false,
            'excludes' => [],

            // مسیرهای اسکن (legacy format) ← عمداً تکرار شده تا هر ورژنی پوشش داده بشه
            'annotations' => [
                base_path('app/Swagger'),
                base_path('Modules/User'),
                base_path('Modules/Package'),
                base_path('Modules/ShipmentRequest'),
            ],
        ],
        'scanOptions' => [
            'exclude' => [base_path('vendor')],
            'pattern' => null,
            'open_api_spec_version' => env('L5_SWAGGER_OPEN_API_SPEC_VERSION', \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
        ],
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('APP_URL', 'http://localhost') . '/api',
        ],
    ],
];
