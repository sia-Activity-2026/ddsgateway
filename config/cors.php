<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CORS Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration controls the CORS (Cross-Origin Resource Sharing)
    | headers that are sent in response to preflight requests.
    |
    */

    // List of allowed origins
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),

    // List of allowed methods
    'allowed_methods' => explode(',', env('CORS_ALLOWED_METHODS', 'GET,POST,PUT,DELETE,OPTIONS,PATCH')),

    // List of allowed headers
    'allowed_headers' => explode(',', env('CORS_ALLOWED_HEADERS', 'Content-Type,Authorization,Accept,X-Requested-With')),

    // List of exposed headers
    'expose_headers' => explode(',', env('CORS_EXPOSE_HEADERS', 'Content-Length,X-JSON-Response-Count')),

    // Whether to allow credentials
    'supports_credentials' => env('CORS_SUPPORTS_CREDENTIALS', false),

    // Max age in seconds for preflight cache
    'max_age' => env('CORS_MAX_AGE', 86400),
];
