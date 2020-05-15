<?php

namespace App;

use App\Events\ReviewFlaggedEvent;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Review
 *
 * @property int $id
 * @property int $user_id
 * @property int $client_id
 * @property string $service_date
 * @property int $star_rating
 * @property string $payment_rating
 * @property string $character_rating
 * @property string $repeat_rating
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activity
 * @property-read Client $client
 * @property-read string|null $flagged_phrase
 * @property-read User $user
 * @property-read int|null $activity_count
 * @method static Builder|Review flagged()
 * @method static Builder|Review newModelQuery()
 * @method static Builder|Review newQuery()
 * @method static Builder|Review query()
 * @mixin Eloquent
 */
class Review extends Model
{
    use SerializesData, LogsActivity;

    const REVIEW_RATING_NO_OPINION = 'No opinion', REVIEW_RATING_THUMBS_UP = 'Thumbs up', REVIEW_RATING_THUMBS_DOWN = 'Thumbs down';
    /**
     * @var string
     */
    protected static $logName = 'review';
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
        "client_id",
        "service_date",
        "star_rating",
        "payment_rating",
        "character_rating",
        "repeat_rating",
        "comment",
        "created_at",
    ];
    /**
     * @var array
     */
    protected $guarded = [];
    protected $appends = ['flagged_phrase'];

    /**
     * @return array
     */
    public static function ratingOptions()
    {
        return [self::REVIEW_RATING_NO_OPINION, self::REVIEW_RATING_THUMBS_DOWN, self::REVIEW_RATING_THUMBS_UP];
    }

    /**
     * @param string $eventName
     * @return string
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $user = auth()->user();
        if ($user) {
            return "Review ID: {$this->id} has been {$eventName} by {$user->email} on Client ID: {$this->client->id}.";
        }
        return "Review ID:{$this->id} has been {$eventName}.";
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
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return void
     */
    public function unFlag()
    {
        FlaggedReview::query()
            ->where('is_resolved', false)
            ->where('review_id', $this->id)
            ->update([
                'is_resolved' => true
            ]);
    }

    /**
     * @return FlaggedReview|boolean
     */
    public function flagIfHasFlaggedPhrases()
    {
        if ($this->hasFlaggedPhrases()) {
            return $this->flag();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasFlaggedPhrases()
    {
        return DB::table('flagged_phrases as fp')->whereRaw("? like concat('%', fp.phrase ,'%')", [$this->comment])
            ->exists();
    }

    /**
     * @return FlaggedReview
     */
    public function flag()
    {
        $phrase = DB::table('flagged_phrases as fp')->whereRaw("? like concat('%', fp.phrase ,'%')", [$this->comment])
            ->first();
        $flagged_review = FlaggedReview::query()
            ->create([
                'review_id' => $this->id,
                'is_resolved' => false,
                'phrase' => $phrase->phrase,
            ]);
        event(new ReviewFlaggedEvent($this));
        return $flagged_review;
    }

    public function scopeFlagged(Builder $builder)
    {
        return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) use ($builder) {
            $query->selectRaw('1')
                ->from('flagged_reviews as fr')
                ->whereRaw('fr.review_id = ' . $builder->qualifyColumn('id'))
                ->where('fr.is_resolved', false);
        });
    }

    /**
     * @return string|null
     */
    public function getFlaggedPhraseAttribute()
    {
        if ($this->isFlagged()) {
            return FlaggedReview::query()
                ->where('is_resolved', false)
                ->where('review_id', $this->id)
                ->pluck('phrase')
                ->first();
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isFlagged()
    {
        return FlaggedReview::query()
            ->where('is_resolved', false)
            ->where('review_id', $this->id)
            ->exists();
    }
}
