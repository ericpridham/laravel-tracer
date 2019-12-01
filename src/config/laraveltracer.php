<?php

return [
    'enabled' => env('LARAVEL_TRACER_ENABLED', false),
    'honeycomb' => [
        'key' => env('HONEYCOMB_KEY'),
        'dataset' => env('HONEYCOMB_DATASET'),
    ]
];