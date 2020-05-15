<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Plan
 *
 * @property int $id
 * @property string $name
 * @property float $price
 * @property string|null $stripe_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Plan newModelQuery()
 * @method static Builder|Plan newQuery()
 * @method static Builder|Plan query()
 * @mixin Eloquent
 */
class Plan extends Model
{
    use SerializesData;

    const SUBSCRIPTION_MAIN = 'main';

    protected $guarded = [];
}
