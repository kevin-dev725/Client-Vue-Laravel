<?php

namespace App;


class clientDomain
{
    public static function scriptVariables()
    {
        /**
         * @var User $user
         */
        $user = auth()->user();
        return [
            'state' => [
                'user' => $user ? $user->getSerializedData(null, null, 'subscription') : null,
                'config' => [
                    'app' => [
                        'name' => config('app.name'),
                        'env' => config('app.env'),
                    ],
                    'settings' => config('settings'),
                    'keys' => [
                        'stripe' => config('services.stripe.key')
                    ],
                    'services' => [
                        'stripe' => [
                            'plan' => config('services.stripe.plan'),
                            'yearly_plan' => config('services.stripe.yearly_plan')
                        ]
                    ]
                ],
                'csrf' => csrf_token(),
            ]
        ];
    }
}