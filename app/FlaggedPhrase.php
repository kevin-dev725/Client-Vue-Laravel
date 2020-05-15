<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\FlaggedPhrase
 *
 * @property int $id
 * @property string $phrase
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|FlaggedPhrase newModelQuery()
 * @method static Builder|FlaggedPhrase newQuery()
 * @method static Builder|FlaggedPhrase query()
 * @mixin Eloquent
 */
class FlaggedPhrase extends Model
{
    protected $guarded = [];
}
