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

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'plan' => [
            'id' => env('STRIPE_PLAN_ID'),
            'price' => env('STRIPE_PLAN_PRICE')
        ],
        'yearly_plan' => [
            'id' => env('STRIPE_YEARLY_PLAN_ID'),
            'price' => env('STRIPE_YEARLY_PLAN_PRICE'),
        ]
    ],

	'facebook' => [
		'client_id' => env('FACEBOOK_KEY'),
		'client_secret' => env('FACEBOOK_SECRET'),
		'redirect' => null,
	],

    'quickbooks' => [
        'client_id' => env('QUICKBOOKS_KEY'),
        'client_secret' => env('QUICKBOOKS_SECRET'),
        'redirect' => null,
    ],

    'google' => [
        'client_id' => env('GOOGLE_ID'),
        'client_secret' => env('GOOGLE_SECRET'),
        'redirect' => null,
    ],
];
