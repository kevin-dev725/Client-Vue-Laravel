<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Skill
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Skill newModelQuery()
 * @method static Builder|Skill newQuery()
 * @method static Builder|Skill query()
 * @mixin Eloquent
 */
class Skill extends Model
{
    protected $guarded = [];
}
