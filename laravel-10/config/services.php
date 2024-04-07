<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
     */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'economic' => [
        'X-AppSecretToken' => env('X_APP_SECRET_TOKEN'),
        'X-AgreementGrantToken' => env('X_AGREEMENT_GRANT_TOKEN'),
    ],

    'agora' => [
        'appID' => env('AGORA_APPID'),
        'appCertificate' => env('AGORA_APP_CERTIFICATE'),
    ],

    'jira' => [
        'host' => env('JIRA_HOST'),
        'username' => env('JIRA_USER'),
        'password' => env('JIRA_PASS'),
    ],

    'vonage' => [
        'apiKey' => env('VONAGE_API_KEY'),
        'apiSecret' => env('VONAGE_API_SECRET'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    
        // optional guzzle specific configuration
        'guzzle' => [
            'verify' => true,
            'decode_content' => true,
        ],
        'options' => [
            // configure endpoint, if not default
            'endpoint' => env('SPARKPOST_ENDPOINT'),
    
            // optional Sparkpost API options go here
            'return_path' => 'mail@bounces.domain.com',
            'options' => [
                'open_tracking' => false,
                'click_tracking' => false,
                'transactional' => true,
            ],
        ],
    ],

    'sproom' => [
        'apiEndPoint' => env('SPROOM_API_ENDPOINT'),
        'apiKey' => env('SPROOM_API_KEY')
    ],
];
