<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\FlaggedReview
 *
 * @property int $id
 * @property int $review_id
 * @property int $is_resolved
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $phrase
 * @method static Builder|FlaggedReview newModelQuery()
 * @method static Builder|FlaggedReview newQuery()
 * @method static Builder|FlaggedReview query()
 * @mixin Eloquent
 */
class FlaggedReview extends Model
{
    protected $guarded = [];
}
