<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\LienFile
 *
 * @property int $id
 * @property int $lien_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|LienFile newModelQuery()
 * @method static Builder|LienFile newQuery()
 * @method static Builder|LienFile query()
 * @mixin Eloquent
 * @property string $file_name
 */
class LienFile extends Model
{
    protected $guarded = [];
}
