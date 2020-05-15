<?php

namespace App;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Invoice
 *
 * @property int $id
 * @property int $user_id
 * @property string $stripe_id
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Invoice newModelQuery()
 * @method static Builder|Invoice newQuery()
 * @method static Builder|Invoice query()
 * @mixin Eloquent
 */
class Invoice extends Model
{
    protected $guarded = [];

    /**
     * @return float
     */
    public static function getTotalEarnings()
    {
        return Invoice::query()
            ->sum('amount');
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @return float
     */
    public static function getTotalEarningsBetween(Carbon $from, Carbon $to)
    {
        return Invoice::query()
            ->whereDate('created_at', '>=', $from->toDateString())
            ->whereDate('created_at', '<=', $to->toDateString())
            ->sum('amount');
    }
}
