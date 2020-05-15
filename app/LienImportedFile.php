<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * App\LienImportedFile
 *
 * @property int $id
 * @property string $file_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Media[] $media
 * @property-read int|null $media_count
 * @method static Builder|LienImportedFile newModelQuery()
 * @method static Builder|LienImportedFile newQuery()
 * @method static Builder|LienImportedFile query()
 * @mixin Eloquent
 */
class LienImportedFile extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $guarded = [];
}
