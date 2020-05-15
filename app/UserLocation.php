<?php

namespace App;

use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\UserLocation
 *
 * @property int $id
 * @property int $user_id
 * @property float $lat
 * @property float $lng
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|UserLocation newModelQuery()
 * @method static Builder|UserLocation newQuery()
 * @method static Builder|UserLocation query()
 * @method static Builder|UserLocation withinRadius($lat, $lng, $radius)
 * @mixin Eloquent
 */
class UserLocation extends Model
{
    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Filter locations within radius(miles).
     *
     * @param Builder $builder
     * @param float $lat
     * @param float $lng
     * @param float $radius in miles
     */
    public function scopeWithinRadius(Builder $builder, float $lat, float $lng, float $radius)
    {
        $builder->selectRaw($builder->qualifyColumn('*') . ", 69.000142695 * DEGREES(ACOS(COS(RADIANS(latpoint))
                 * COS(RADIANS(lat))
                 * COS(RADIANS(longpoint) - RADIANS(lng))
                 + SIN(RADIANS(latpoint))
                 * SIN(RADIANS(lat)))) AS distance")
            ->joinSub("SELECT {$lat} AS latpoint, {$lng} AS longpoint", 'p', DB::raw(1), '=', DB::raw(1))
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }
}
