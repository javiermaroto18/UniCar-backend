<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(array_map('trim', explode(',', env(
        'CORS_ALLOWED_ORIGINS',
        'http://localhost:5173,http://127.0.0.1:5173'
    )))),

    // Permite cualquier despliegue del proyecto en Vercel (las URLs de preview
    // cambian en cada deploy: unicar-frontend-<hash>-<team>.vercel.app).
    // Configurable por env; por defecto cubre el proyecto unicar-frontend.
    'allowed_origins_patterns' => array_filter(array_map('trim', explode('|||', env(
        'CORS_ALLOWED_ORIGINS_PATTERNS',
        '#^https://unicar-frontend-.*\.vercel\.app$#'
    )))),

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
