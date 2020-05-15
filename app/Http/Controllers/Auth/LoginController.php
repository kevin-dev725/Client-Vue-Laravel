<?php

namespace App\Http\Controllers\Auth;

use App\Http\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\ApiTokenCookieFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LoginController
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * @var ApiTokenCookieFactory
     */
    protected $cookieFactory;

    /**
     * Create a new controller instance.
     *
     * @param ApiTokenCookieFactory $cookieFactory
     */
    public function __construct(ApiTokenCookieFactory $cookieFactory)
    {
        $this->cookieFactory = $cookieFactory;
        $this->middleware('guest')->except('logout');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|Response
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {

            // Check if the user uses a temporary password and check if it is expired
            if ($this->guard()->user()->usesTemporaryPassword() &&
                $this->guard()->user()->expiredTemporaryPassword()) {

                $this->incrementLoginAttempts($request);
                return $this->sendExpiredLoginResponse($request);
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    protected function sendExpiredLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.expired_temp_password')],
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    protected function authenticated(Request $request, User $user)
    {
        $response = ApiResponse::success([
            'user' => $user->getSerializedData($request)
        ]);
        $user->update([
            'last_site_signin' => now()
        ]);
        $response->headers
            ->setCookie($this->cookieFactory->make(
                $user->getKey(), $request->session()->token()
            ));
        return $response;
    }

       /**
     * @param Request $request
     * @throws ValidationException
     */
    protected function sendLockedAccountResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.locked_account')],
        ]);
    }


}
