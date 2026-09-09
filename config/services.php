<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    | API externa de producción (Exequiales y otros módulos legados). Se centraliza aquí en vez
    | de usar env() directo en los controladores porque env() fuera de config/*.php devuelve
    | null si algún día se ejecuta `php artisan config:cache` — un caso muy fácil de olvidar en
    | despliegues futuros y que dejaría sin autenticación (silenciosamente) todas las llamadas
    | a esta API.
    */
    'api_produccion' => [
        'url' => env('API_PRODUCCION'),
        'token' => env('TOKEN_ADMIN'),
    ],

    /*
    | API "Corpentunida CRM" — sistema distinto al de arriba (siasoft). Usa OAuth2
    | client_credentials: un token JWT que vence a las 4 horas y se debe renovar,
    | en vez de un token estático fijo como el de api_produccion.
    */
    'corpentunida_crm' => [
        'url' => env('CORPENTUNIDA_CRM_URL'),
        'client_id' => env('CORPENTUNIDA_CRM_CLIENT_ID'),
        'client_secret' => env('CORPENTUNIDA_CRM_CLIENT_SECRET'),
    ],

];
