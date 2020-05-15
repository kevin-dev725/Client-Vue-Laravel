<?php

namespace App;

use App\Exceptions\User\ErrorUploadingAvatar;
use App\Exceptions\User\InvalidSocialProvider;
use App\Traits\Encryptable;
use Carbon\Carbon;
use Eloquent;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Kozz\Laravel\Facades\Guzzle;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\HasActivity;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * App\User
 *
 * @property int $id
 * @property int $is_free_account
 * @property string|null $name
 * @property string $email
 * @property string|null $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $role_id
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $account_type
 * @property string|null $company_name
 * @property string|null $description
 * @property string|null $phone_number
 * @property string|null $phone_number_ext
 * @property string|null $alt_phone_number
 * @property string|null $alt_phone_number_ext
 * @property string|null $street_address
 * @property string|null $street_address2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $business_url
 * @property string|null $facebook_url
 * @property string|null $twitter_url
 * @property string|null $expiry_date
 * @property string|null $account_status
 * @property string|null $last_app_signin
 * @property string|null $last_site_signin
 * @property string|null $password_raw
 * @property string $avatar_path
 * @property string|null $social_provider
 * @property string|null $social_id
 * @property string|null $social_profile_url
 * @property string|null $social_image_url
 * @property string|null $social_token
 * @property string|null $social_token_secret
 * @property string|null $social_refresh_token
 * @property string|null $social_expires_in
 * @property string|null $stripe_id
 * @property string|null $card_brand
 * @property string|null $card_last_four
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property string|null $temp_password_exp_date
 * @property int|null $company_id
 * @property int|null $country_id
 * @property string|null $overview
 * @property string|null $license_number
 * @property int|null $is_insured
 * @property-read Collection|Activity[] $actions
 * @property-read Collection|Activity[] $activity
 * @property-read Collection|ClientImport[] $client_imports
 * @property-read Collection|Client[] $clients
 * @property-read Company|null $company
 * @property-read Country|null $country
 * @property-read Collection|Media[] $gallery_photos
 * @property-read bool $is_on_trial
 * @property-read Collection|Invoice[] $invoice_models
 * @property-read Collection|Media[] $media
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read Collection|QuickbooksImport[] $quickbooks_imports
 * @property-read Collection|Review[] $reviews
 * @property-read Role $role
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read Collection|Token[] $tokens
 * @property-read Collection|Skill[] $user_skills
 * @property-read Collection|License[] $licenses
 * @property-read string $skills
 * @property-read string $full_address
 * @property-read UserLocation $location
 * @property-read int|null $actions_count
 * @property-read int|null $activity_count
 * @property-read int|null $client_imports_count
 * @property-read int|null $clients_count
 * @property-read int|null $gallery_photos_count
 * @property-read int|null $invoice_models_count
 * @property-read int|null $licenses_count
 * @property-read int|null $media_count
 * @property-read int|null $notifications_count
 * @property-read int|null $quickbooks_imports_count
 * @property-read int|null $reviews_count
 * @property-read int|null $subscriptions_count
 * @property-read int|null $tokens_count
 * @property-read int|null $user_skills_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User subscribed()
 * @method static Builder|User users()
 * @mixin Eloquent
 */
class User extends Authenticatable implements HasMedia
{
    use Notifiable, SerializesData, HasApiTokens, Notifiable, Billable, Encryptable, HasActivity, HasMediaTrait;

    const ACCOUNT_TYPE_INDIVIDUAL = 'individual', ACCOUNT_TYPE_COMPANY = 'company';
    const ACCOUNT_STATUS_ACTIVE = 'active', ACCOUNT_STATUS_SUSPENDED = 'suspended', ACCOUNT_STATUS_EXPIRED = 'expired', ACCOUNT_STATUS_CANCELLED = 'cancelled';
    const SOCIAL_PROVIDER_FACEBOOK = 'facebook', SOCIAL_PROVIDER_GOOGLE = 'google', SOCIAL_PROVIDER_TWITTER = 'twitter';
    const MEDIA_COLLECTION_GALLERY = 'gallery';

    /**
     * @var string
     */
    protected static $logName = 'user';
    /**
     * @var array
     */
    protected static $ignoreChangedAttributes = [
        'updated_at',
        'remember_token',
        'role_id',
        'last_app_signin',
        'last_site_signin'
    ];

    /**
     * @var array
     */
    protected static $logAttributes = [
        'name',
        'email',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'account_type',
        'company_name',
        'description',
        'phone_number',
        'phone_number_ext',
        'alt_phone_number',
        'alt_phone_number_ext',
        'street_address',
        'street_address2',
        'city',
        'state',
        'postal_code',
        'business_url',
        'facebook_url',
        'twitter_url',
        'expiry_date',
        'account_status',
        'password_raw',
        'avatar_path',
        'social_provider',
        'social_id',
        'social_profile_url',
        'social_profile_image',
        'social_expires_in',
        'stripe_id',
        'card_brand',
        'card_last_four',
        'trial_ends_at',
        'temp_password_exp_date',
        'company_id',
        'country_id',
        'created_at'
    ];

    /**
     * @var bool
     */
    protected static $logOnlyDirty = true;

    /**
     * The attributes that are black listed.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'role_id',
        'social_token',
        'social_token_secret',
        'password_raw'
    ];

    protected $dates = [
        'trial_ends_at'
    ];

    /**
     * @var array
     */
    protected $encrypts = ['social_token', 'social_token_secret'];

    protected $appends = [
        'is_on_trial',
        'skills',
    ];

    /**
     * @param $provider
     * @param $socialite_user
     * @return User
     * @throws InvalidSocialProvider
     */
    public static function getInstanceFromSocialiteUser($provider, \Laravel\Socialite\Contracts\User $socialite_user)
    {
        /**
         * @var User $user
         */
        $user = User::query()
            ->where('email', $socialite_user->getEmail())
            ->first();
        if ($user) {
            return $user;
        }
        switch ($provider) {
            case User::SOCIAL_PROVIDER_FACEBOOK:
                $fields = [
                    'role_id' => Role::ROLE_USER,
                    'account_type' => self::ACCOUNT_TYPE_INDIVIDUAL,
                    'social_provider' => $provider,
                    'social_id' => $socialite_user->id,
                    'email' => $socialite_user->email,
                    'name' => $socialite_user->name,
                    'social_profile_url' => $socialite_user->profileUrl,
                    'social_image_url' => $socialite_user->avatar_original,
                    'social_token' => $socialite_user->token,
                    'social_refresh_token' => $socialite_user->refreshToken,
                    'facebook_url' => $socialite_user->profileUrl,
                ];
                if (isset($socialite_user->user['first_name'])) {
                    $fields['first_name'] = $socialite_user->user['first_name'];
                }
                if (isset($socialite_user->user['last_name'])) {
                    $fields['last_name'] = $socialite_user->user['last_name'];
                }

                $user = User::query()
                    ->create($fields);
                break;
            case User::SOCIAL_PROVIDER_GOOGLE:
                $fields = [
                    'role_id' => Role::ROLE_USER,
                    'account_type' => self::ACCOUNT_TYPE_INDIVIDUAL,
                    'social_provider' => $provider,
                    'social_id' => $socialite_user->id,
                    'email' => $socialite_user->email,
                    'name' => $socialite_user->name,
                    'social_image_url' => $socialite_user->avatar_original,
                    'social_token' => $socialite_user->token,
                    'social_refresh_token' => $socialite_user->refreshToken,
                    'social_expires_in' => $socialite_user->expiresIn,
                ];
                if (isset($socialite_user->user['given_name'])) {
                    $fields['first_name'] = $socialite_user->user['given_name'];
                }
                if (isset($socialite_user->user['family_name'])) {
                    $fields['last_name'] = $socialite_user->user['family_name'];
                }
                $user = User::query()
                    ->create($fields);
                break;
            default:
                throw new InvalidSocialProvider();
                break;
        }
        return $user;
    }

    /**
     * @param string $eventName
     * @return string
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $description = "{$this->email} is now registered to the website.";
        if ($eventName === 'updated') {
            $description = "{$this->email}'s profile was updated.";
        }
        /**
         * @var User $user
         */
        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            $description = "{$this->email} has been {$eventName} by {$user->email}.";
        }
        return $description;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role_id === Role::ROLE_ADMIN;
    }

    /**
     * @return HasOne
     */
    public function role()
    {
        return $this->hasOne(Role::class);
    }

    /**
     * @return HasMany
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    /**
     * @return HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasOne
     */
    public function location()
    {
        return $this->hasOne(UserLocation::class);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeUsers(Builder $query)
    {
        return $query->where('role_id', Role::ROLE_USER);
    }

    /**
     * @return bool
     */
    public function isUser()
    {
        return $this->role_id === Role::ROLE_USER;
    }

    /**
     * @param Review $review
     * @return bool
     */
    public function ownsReview(Review $review)
    {
        return $this->id === $review->user->id;
    }

    /**
     * Check if user is registered through social network account.
     * @return bool
     */
    public function isSocialAccount()
    {
        return !empty($this->social_provider);
    }

    /**
     * @param UploadedFile $file
     * @return User
     * @throws ErrorUploadingAvatar
     */
    public function saveAvatar(UploadedFile $file)
    {
        if ($path = disk_s3()->putFile($this->avatarDirectory(), $file, 'public')) {
            $this->avatar_path = $path;
        } else {
            throw new ErrorUploadingAvatar();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function avatarDirectory()
    {
        return storage_prefix_dir($this->id . '/avatar');
    }

    /**
     * @param $url
     * @return User
     * @throws ErrorUploadingAvatar
     */
    public function saveAvatarFromUrl($url)
    {
        /**
         * @var Response $response
         */
        $response = Guzzle::get($url);
        $content_type = $response->getHeader('Content-Type')[0];
        if (disk_s3()->put($path = $this->avatarDirectory() . '/' . uniqueFilename(getMimeExtension($content_type)), $response->getBody()->getContents(), 'public')) {
            $this->avatar_path = $path;
        } else {
            throw new ErrorUploadingAvatar();
        }
        return $this;
    }

    /**
     * @param $value
     * @return string
     */
    public function getAvatarPathAttribute($value)
    {
        if (empty($value)) return null;
        return disk_s3()->url($value);
    }

    /**
     * @return bool
     */
    public function hasAvatar()
    {
        return !empty($this->avatar_path);
    }

    /**
     * @return bool
     */
    public function usesTemporaryPassword()
    {
        return !empty($this->temp_password_exp_date);
    }

    /**
     * @return bool
     */
    public function expiredTemporaryPassword()
    {
        return Carbon::parse($this->temp_password_exp_date)->lt(Carbon::now());
    }

    /**
     * @return bool
     */
    public function isAccountCompany()
    {
        return $this->account_type === self::ACCOUNT_TYPE_COMPANY && $this->company;
    }

    /**
     * @return bool
     */
    public function isAccountIndividual()
    {
        return $this->account_type === self::ACCOUNT_TYPE_INDIVIDUAL && !$this->company;
    }

    /**
     * @return bool|Builder|Model|null|object
     */
    public function getSubscribedPlan()
    {
        if (!$this->subscribed(Plan::SUBSCRIPTION_MAIN)) {
            return false;
        }
        $plan = Plan::query()
            ->where('stripe_id',
                $this->subscriptions()
                    ->first()
                    ->stripe_plan)
            ->first();

        return $plan;
    }

    /**
     * @return string
     */
    public function getFullStreetAddress()
    {
        return trim($this->street_address . ' ' . $this->street_address2);
    }

    /**
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $components = [
            $this->street_address,
            $this->street_address2,
            $this->city,
            $this->state,
            $this->postal_code,
        ];
        $components = array_map('trim', $components);
        $components = array_filter($components, function ($str) {
            return $str != '';
        });
        return implode(', ', $components);
    }

    /**
     * @return bool
     */
    public function finishedSignup()
    {
        return !empty($this->city);
    }

    /**
     * @return BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return HasMany
     */
    public function client_imports()
    {
        return $this->hasMany(ClientImport::class);
    }

    /**
     * @return HasMany
     */
    public function quickbooks_imports()
    {
        return $this->hasMany(QuickbooksImport::class);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSubscribed(Builder $query)
    {
        return $query->whereHas('subscriptions', function ($q1) {
            $q1->where(function ($q2) {
                $q2->whereNull('ends_at')
                    ->orWhere('ends_at', '>', Carbon::now())
                    ->orWhereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '>', Carbon::today());
            });
        });
    }

    /**
     * @return HasMany
     */
    public function invoice_models()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * @return MorphMany
     */
    public function gallery_photos()
    {
        return $this->morphMany(Media::class, 'model')
            ->where('collection_name', self::MEDIA_COLLECTION_GALLERY);
    }

    /**
     * @return BelongsToMany
     */
    public function user_skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills', 'user_id', 'skill_id')
            ->withTimestamps();
    }

    /**
     * @param string $skills
     */
    public function saveSkills($skills)
    {
        $skills = explode(',', $skills);
        $skill_models = [];
        foreach ($skills as $skill) {
            $skill_models[] = Skill::firstOrCreate([
                'name' => $skill
            ])->id;
        }
        $this->user_skills()
            ->sync($skill_models);
    }

    /**
     * @return HasMany
     */
    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    /**
     * Get comma-separated skills.
     *
     * @return string
     */
    public function getSkillsAttribute()
    {
        if (!Schema::hasTable('skills')) {
            return null;
        }
        $names = $this->user_skills()
            ->pluck('name')
            ->all();
        return implode(',', $names);
    }

    /**
     * @return bool
     */
    public function getIsOnTrialAttribute()
    {
        if (Schema::hasTable('subscriptions')) {
            return $this->onTrial();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->account_status == self::ACCOUNT_STATUS_ACTIVE;
    }
    
}
