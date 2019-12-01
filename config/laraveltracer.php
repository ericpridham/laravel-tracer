<?php

return [
    'enabled' => env('LARAVEL_TRACER_ENABLED', false),
    'rootName' => env('LARAVEL_TRACER_ROOT_NAME', 'app'),
    'honeycomb' => [
        'key' => env('HONEYCOMB_KEY'),
        'dataset' => env('HONEYCOMB_DATASET'),
    ]
];