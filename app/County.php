<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\County
 *
 * @property int $id
 * @property int $state_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|County newModelQuery()
 * @method static Builder|County newQuery()
 * @method static Builder|County query()
 * @mixin Eloquent
 */
class County extends Model
{
    //
}
