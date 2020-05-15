<?php

namespace App\Http\Controllers;

use App\Exceptions\User\ErrorUploadingAvatar;
use App\Exceptions\User\InvalidSocialProvider;
use App\RegisterToken;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SocialController extends Controller
{

    protected $redirect_url = '/account';
    protected $home_url = '/dashboard/settings';

    public function handle(Request $request, $provider)
    {
        $method = 'handle' . studly_case($provider);

        if (method_exists($this, $method)) {
            return $this->{$method}($request);
        } else {
            $this->missingMethod();
        }
    }

    public function missingMethod()
    {
        throw new NotFoundHttpException();
    }

    /**
     * @param Request $request
     * @param $provider
     * @return RedirectResponse|Redirector
     * @throws ErrorUploadingAvatar
     * @throws InvalidSocialProvider
     */
    public function handleCallback(Request $request, $provider)
    {
        if ($provider === 'facebook') {
            $socialite_user = Socialite::driver($provider)
                ->fields(['name', 'first_name', 'last_name', 'email', 'verified', 'link'])
                ->user();
        } else {
            $socialite_user = Socialite::driver($provider)->user();
        }
        if (!$socialite_user) {
            throw new NotFoundHttpException("Invalid access token.");
        }

        $user = User::getInstanceFromSocialiteUser($provider, $socialite_user);

        if (!empty($user->social_image_url) && !$user->hasAvatar()) {
            $user->saveAvatarFromUrl($user->social_image_url)
                ->save();
        }
        auth()->login($user);
        if (!$user->finishedSignup()) {
            return $this->finishSignup();
        }
        return $this->redirectToHome();
    }

    protected function finishSignup()
    {
        return redirect('/register/finish');
    }

    /**
     * @return RedirectResponse|Redirector
     */
    protected function redirectToHome()
    {
        return redirect($this->home_url);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function handleFacebook(Request $request)
    {
        return Socialite::with(User::SOCIAL_PROVIDER_FACEBOOK)
            ->scopes(['email', 'public_profile'])
            ->redirect();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function handleTwitter(Request $request)
    {
        return Socialite::with(User::SOCIAL_PROVIDER_TWITTER)->redirect();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function handleLinkedin(Request $request)
    {
        return Socialite::with(User::SOCIAL_PROVIDER_LINKEDIN)->redirect();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function handleGoogle(Request $request)
    {
        return Socialite::with(User::SOCIAL_PROVIDER_GOOGLE)
            ->scopes([
                'https://www.googleapis.com/auth/plus.login',
                'https://www.googleapis.com/auth/plus.me',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/user.birthday.read'
            ])
            ->redirect();
    }
}
