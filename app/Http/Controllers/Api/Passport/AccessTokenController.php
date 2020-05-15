<?php

namespace App\Http\Controllers\Api\Passport;

use App\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Http\Controllers\AccessTokenController as PassportAccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Auth\AuthenticationException;

class AccessTokenController extends PassportAccessTokenController
{
    public function issueToken(ServerRequestInterface $request)
    {
        if (request('grant_type') == 'password') {
            /**
             * @var User $user
             */
            $user = User::query()
                ->where('email', request('username'))
                ->first();
            if (!$user) {
                throw new AuthenticationException('The email or password is not correct.');
                return;
            }
            if (!$user->isActive()) {
                throw new AuthenticationException('Your account is '. $user->account_status. '.');
                return;
            }
            if ($user && Hash::check(request('password'), $user->password)) {
                $user->update([
                    'last_app_signin' => now()
                ]);
            }
        }
        return parent::issueToken($request);
    }
}
