<?php

return [
    'max_image_upload' => 10 * 1024,
	'import_allowed_extensions' => [
		'csv'
	],
    'default_country' => 'US',
    'import' => [
        'default_initial_star_rating' => 4,
    ],
    'mobile_apps' => [
        'android' => 'https://play.google.com/store/apps/details?id=app.clientDomain.app',
        'ios' => 'https://itunes.apple.com/us/app/clientDomain/id1425725334?mt=8',
    ],
    'subscription' => [
        'trial_days' => 21,
    ],
    'free_account_on_register_enabled' => env('FREE_ACCOUNT_ON_REGISTER_ENABLED', false),
];
