<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HafizPlus API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi ini dipakai untuk hardening API v1:
    | - rate limit API umum
    | - rate limit login
    | - token expiration policy
    | - allowed CORS origins untuk development/frontend/mobile
    |
    */

    'api' => [
        'rate_limit_per_minute' => (int) env('HAFIZPLUS_API_RATE_LIMIT_PER_MINUTE', 60),

        'login_rate_limit_per_minute' => (int) env('HAFIZPLUS_API_LOGIN_RATE_LIMIT_PER_MINUTE', 5),

        'token_expiration_days' => (int) env('HAFIZPLUS_API_TOKEN_EXPIRATION_DAYS', 30),

        'allowed_origins' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env(
                'HAFIZPLUS_CORS_ALLOWED_ORIGINS',
                'http://localhost:3000,http://127.0.0.1:3000,http://localhost:5173,http://127.0.0.1:5173'
            ))
        ))),
    ],

];