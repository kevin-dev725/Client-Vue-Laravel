<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Client
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $organization_name
 * @property string $name
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $phone_number
 * @property string|null $phone_number_ext
 * @property string|null $alt_phone_number
 * @property string|null $alt_phone_number_ext
 * @property string|null $street_address
 * @property string|null $street_address2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $email
 * @property string|null $billing_first_name
 * @property string|null $billing_middle_name
 * @property string|null $billing_last_name
 * @property string|null $billing_phone_number
 * @property string|null $billing_phone_number_ext
 * @property string|null $billing_street_address
 * @property string|null $billing_street_address2
 * @property string|null $billing_city
 * @property string|null $billing_state
 * @property string|null $billing_postal_code
 * @property string|null $billing_email
 * @property int|null $initial_star_rating
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $client_type
 * @property int|null $company_id
 * @property int|null $country_id
 * @property-read Collection|Activity[] $activity
 * @property-read Company|null $company
 * @property-read Country|null $country
 * @property-read Collection|Review[] $reviews
 * @property-read User|null $user
 * @property-read int|null $activity_count
 * @property-read int|null $reviews_count
 * @method static Builder|Client newModelQuery()
 * @method static Builder|Client newQuery()
 * @method static Builder|Client query()
 * @method static Builder|Client unReviewed()
 * @mixin Eloquent
 */
class Client extends Model
{
    use SerializesData, LogsActivity;

    const CLIENT_TYPE_INDIVIDUAL = 'individual', CLIENT_TYPE_ORGANIZATION = 'organization';
    const SEARCH_BY_PHONE = 1, SEARCH_BY_EMAIL = 2, SEARCH_BY_NAME_ADDRESS = 3;
    /**
     * @var string
     */
    protected static $logName = 'client';
    /**
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * @var array
     */
    protected static $ignoreChangedAttributes = ['updated_at'];
    /**
     * @var array
     */
    protected static $logAttributes = [
        "user_id",
        "organization_name",
        "name",
        "first_name",
        "middle_name",
        "last_name",
        "phone_number",
        "phone_number_ext",
        "alt_phone_number",
        "alt_phone_number_ext",
        "street_address",
        "street_address2",
        "city",
        "state",
        "postal_code",
        "email",
        "billing_first_name",
        "billing_middle_name",
        "billing_last_name",
        "billing_phone_number",
        "billing_phone_number_ext",
        "billing_street_address",
        "billing_street_address2",
        "billing_city",
        "billing_state",
        "billing_postal_code",
        "billing_email",
        "initial_star_rating",
        "created_at",
        "updated_at",
        "client_type",
        "company_id",
        "country_id",
    ];
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @param string $eventName
     * @return string
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $user = auth()->user();
        if ($user) {
            return "{$this->email} has been {$eventName} by {$user->email}.";
        }
        if (!$user && $this->user) {
            return "{$this->email} has been {$eventName} by {$this->user->email}";
        }
        return "{$this->email} has been {$eventName}.";
    }


    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return mixed
     */
    public function getAvgRating()
    {
        $avg = $this->all_reviews()
            ->avg('reviews.star_rating');
        return $avg <= 0 ? $this->initial_star_rating : $avg;
    }

    /**
     * @return Builder
     */
    public function all_reviews()
    {
        return Review::query()
            ->leftJoin('clients as c', 'c.id', '=', 'reviews.client_id')
            ->where('c.first_name', $this->first_name)
            ->where('c.last_name', $this->last_name)
            ->where('c.phone_number', $this->phone_number)
            ->where('c.email', $this->email)
            ->select('reviews.*')
            ->orderBy('reviews.created_at', 'desc');
    }

    public function scopeUnReviewed(Builder $builder)
    {
        return $builder->whereNotExists(function (\Illuminate\Database\Query\Builder $builder) {
            $builder->select(DB::raw(1))
                ->from('clients as c')
                ->leftJoin('reviews', 'reviews.client_id', '=', 'c.id')
                ->whereRaw('c.first_name = clients.first_name')
                ->whereRaw('c.last_name = clients.last_name')
                ->whereRaw('c.phone_number = clients.phone_number')
                ->whereRaw('c.email = clients.email')
                ->whereNotNull('reviews.id');
        });
    }

    /**
     * @return bool
     */
    public function isOrganization()
    {
        return $this->client_type === self::CLIENT_TYPE_ORGANIZATION;
    }

    /**
     * @return BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return Review
     */
    public function createInitialReview()
    {
        return $this->reviews()
            ->create([
                'user_id' => $this->user_id,
                'service_date' => today()->toDateString(),
                'star_rating' => config('settings.import.default_initial_star_rating'),
                'payment_rating' => Review::REVIEW_RATING_NO_OPINION,
                'character_rating' => Review::REVIEW_RATING_NO_OPINION,
                'repeat_rating' => Review::REVIEW_RATING_NO_OPINION,
            ]);
    }

    /**
     * @return HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
