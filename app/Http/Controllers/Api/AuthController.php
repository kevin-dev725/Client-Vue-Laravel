<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\User\ErrorUploadingAvatar;
use App\Exceptions\User\InvalidSocialProvider;
use App\Http\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\LoginSocialRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\RequestResetPasswordRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\TempPasswordRequest;
use App\Http\Requests\Api\Auth\UpdateContactRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\SES\SESMail;
use App\SES\EmailTemplate;

use App\Notifications\PasswordReset;
use App\Notifications\TempPassword;
use App\Plan;
use App\Role;
use App\Transformers\UserTransformer;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\AuthenticationException;

class AuthController extends Controller
{
    /**
     * @var UserTransformer
     */
    private $transformer;

    /**
     * AuthController constructor.
     * @param UserTransformer $transformer
     */
    public function __construct(UserTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function user(Request $request)
    {
        return $request->user()
            ->getSerializedData($request);
    }

    /**
     * @param UpdateContactRequest $request
     * @return mixed
     * @throws Exception
     */
    public function updateContact(UpdateContactRequest $request)
    {
        beginTransaction();
        try {
            $request->user()
                ->update($request->only([
                    'phone_number',
                    'phone_number_ext',
                    'alt_phone_number',
                    'alt_phone_number_ext',
                    'street_address',
                    'street_address2',
                    'city',
                    'state',
                    'postal_code',
                    'email',
                    'business_url',
                    'facebook_url',
                    'twitter_url',
                    'country_id'
                ]));
            commit();
            return $request->user()
                ->getSerializedData($request);
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * @param UpdateProfileRequest $request
     * @return array
     * @throws Exception
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        beginTransaction();
        try {
            /** @var User $user */
            $user = $request->user();
            $user->update(
                $request->only([
                    'first_name',
                    'middle_name',
                    'last_name',
//					    'account_type',
                    'company_name',
                    'description',
                    'email',
                    'phone_number',
                    'phone_number_ext',
                    'alt_phone_number',
                    'alt_phone_number_ext',
                    'street_address',
                    'street_address2',
                    'city',
                    'state',
                    'business_url',
                    'facebook_url',
                    'twitter_url',
                    'country_id',
                    'overview',
                    'license_number',
                    'is_insured',
                ])
            );
            if ($request->has('skills')) {
                $user->saveSkills($request->get('skills'));
            }
            commit();

            return $request->user()
                ->getSerializedData($request);
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * @param ChangePasswordRequest $request
     * @return mixed
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        transaction(function () use ($request) {
            $request->user()
                ->update([
                    'password' => bcrypt($request->get('password')),
                    'password_raw' => null,
                    'temp_password_exp_date' => null,
                ]);
        });

        return $request->user()
            ->getSerializedData($request);
    }

    /**
     * @param LoginSocialRequest $request
     * @return array
     * @throws InvalidSocialProvider
     * @throws ErrorUploadingAvatar
     */
    public function loginSocial(LoginSocialRequest $request)
    {
        $socialite_user = null;
        switch ($request->provider()) {
            case User::SOCIAL_PROVIDER_FACEBOOK:
                $socialite_user = Socialite::driver($request->provider())
                    ->userFromToken($request->access_token());
                break;
            case User::SOCIAL_PROVIDER_GOOGLE:
                $socialite_user = Socialite::driver($request->provider())
                    ->userFromToken($request->access_token());
                break;
            case User::SOCIAL_PROVIDER_TWITTER:
                $socialite_user = Socialite::driver($request->provider())
                    ->userFromTokenAndSecret($request->access_token(),
                        $request->token_secret());
                break;
        }

        $user = User::getInstanceFromSocialiteUser($request->provider(), $socialite_user);

        
        if (!empty($user->social_image_url) && !$user->hasAvatar()) {
            $user->saveAvatarFromUrl($user->social_image_url)
                ->save();
        }
        $token = $user->createToken('password');
        if ($user->account_status && !$user->isActive()) {
            throw new AuthenticationException('This user is ' . $user->account_status . ".");
            return;
        }
        return [
            'token_type' => 'Bearer',
            'access_token' => $token->accessToken,
        ];
    }

    /**
     * @param TempPasswordRequest $request
     * @return JsonResponse
     */
    public function requestTempPassword(TempPasswordRequest $request)
    {
        $user = User::query()
            ->where('email', $request->get('email'))
            ->first();
        $temp_password = str_random(8);

        transaction(function () use ($user, $temp_password) {
            $user->update([
                'password' => bcrypt($temp_password),
                'password_raw' => $temp_password,
                'temp_password_exp_date' => Carbon::now()
                    ->addDays(7),
            ]);
        });

        $user->notify(new TempPassword($temp_password));

        return ApiResponse::success();
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function register(RegisterRequest $request)
    {
        beginTransaction();
        try {
            User::query()
                ->create(array_merge($request->only([
                    'account_type',
                    'email',
                    'first_name',
                    'middle_name',
                    'last_name',
                    'street_address',
                    'street_address2',
                    'city',
                    'state',
                    'postal_code',
                    'phone_number',
                    'phone_number_ext',
                    'alt_phone_number',
                    'alt_phone_number_ext',
                    'country_id'
                ]),
                    [
                        'role_id' => Role::ROLE_USER,
                        'name' => $request->getFullName(),
                        'password' => bcrypt($request->get('password')),
                        'is_free_account' => true,
                    ]
                ));

            commit();

            return ApiResponse::success();
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws ErrorUploadingAvatar
     */
    public function updateAvatar(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|image|max:' . config('settings.max_image_upload')
        ]);
        /**
         * @var User $user
         */
        $user = $request->user();
        $user->saveAvatar($request->file('avatar'))
            ->save();
        return $user->refresh()
            ->getSerializedData($request);
    }

    /**
     * @param Request $request
     */
    public function finishSignup(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:20',
            'middle_name' => 'nullable|string|max:20',
            'last_name' => 'required|string|max:20',
            'phone_number' => 'required|phone:AUTO,US',
            'phone_number_ext' => 'nullable|string|max:10',
            'alt_phone_number' => 'nullable|phone:AUTO,US',
            'alt_phone_number_ext' => 'nullable|string|max:10',
            'street_address' => 'required|string|max:80',
            'street_address2' => 'nullable|string|max:80',
            'city' => 'required|string|max:40',
            'state' => 'required|string|max:2',
            'postal_code' => 'required|string|max:20',
        ];
        if (!config('settings.free_account_on_register_enabled')) {
            $rules['card_token'] = 'required|string|max:255';
        }

        $this->validate($request, $rules);
        transaction(function () use ($request) {
            /**
             * @var User $user
             */
            $user = $request->user();
            $user->update(array_merge(
                $request->only([
                    'first_name',
                    'middle_name',
                    'last_name',
                    'street_address',
                    'street_address2',
                    'city',
                    'state',
                    'postal_code',
                    'phone_number',
                    'phone_number_ext',
                    'alt_phone_number',
                    'alt_phone_number_ext',
                ]),
                [
                    'is_free_account' => config('settings.free_account_on_register_enabled'),
                ]
            ));
            if (!config('settings.free_account_on_register_enabled')) {
                $user->newSubscription(Plan::SUBSCRIPTION_MAIN, config('services.stripe.plan.id'))
                    ->create($request->get('card_token'));
            }
        });
    }

    public function requestResetPassword(RequestResetPasswordRequest $request)
    {
        transaction(function () use ($request) {
            $user = User::where('email', $request->get('email'))
                ->firstOrFail();
            DB::table('password_resets')->where('email', $user->email)->delete();
            if (DB::table('password_resets')
                ->insert([
                    'email' => $user->email,
                    'token' => Hash::make($token = str_random(8)),
                    'created_at' => now()
                ])) {

                $url = url('/reset-password?token=' . urlencode($token) . '&email=' . rawurlencode($user->email));
                $vars = array(
                    '$token' => $token,
                    '$url' => $url,
                );
                $messageContent = EmailTemplate::getPasswordResetTemplate($vars);

                $sesEmailSender  = new SESMail($user->email, "Password Reset", $messageContent);
                
                return $sesEmailSender->sendEmail();
            }
        });
        return ApiResponse::success();
    }


    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
        ]);
        transaction(function () use ($request) {
            $query = DB::table('password_resets')
                ->where('email', $request->get('email'));
            $passwordResets = $query->first();
            User::query()
                ->where('email', $passwordResets->email)
                ->update([
                    'password' => bcrypt($request->get('password')),
                    'password_raw' => null,
                    'temp_password_exp_date' => null,
                ]);
            $query->delete();
        });
        return ApiResponse::success();
    }

    public function verifyResetPasswordToken(ResetPasswordRequest $request)
    {
        return ApiResponse::success();
    }
}
