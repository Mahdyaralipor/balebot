<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bot Token
    |--------------------------------------------------------------------------
    | Your Bale bot token from @BotFather.
    */
    'token' => env('BALE_BOT_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhook
    |--------------------------------------------------------------------------
    */
    'webhook' => [
        'register_route' => true,
        'route_prefix'   => env('BALE_WEBHOOK_PREFIX', 'balebot/webhook'),
        'secret_token'   => env('BALE_WEBHOOK_SECRET', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    */
    'http' => [
        'timeout'         => env('BALE_HTTP_TIMEOUT', 30),
        'connect_timeout' => env('BALE_HTTP_CONNECT_TIMEOUT', 10),
        'proxy'           => env('BALE_HTTP_PROXY', null),
    ],
];
