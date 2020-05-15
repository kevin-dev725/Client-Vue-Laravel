<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * App\License
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $number
 * @property string $expiration
 * @property int $is_insured
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Media[] $certs
 * @property-read Collection|Media[] $media
 * @property-read Collection|Media[] $photos
 * @property-read int|null $certs_count
 * @property-read int|null $media_count
 * @property-read int|null $photos_count
 * @method static Builder|License newModelQuery()
 * @method static Builder|License newQuery()
 * @method static Builder|License query()
 * @mixin Eloquent
 */
class License extends Model implements HasMedia
{
    use HasMediaTrait;

    const MEDIA_COLLECTION_PHOTOS = 'photos';
    const MEDIA_COLLECTION_CERTS = 'certs';

    protected $guarded = [];

    /**
     * @return MorphMany
     */
    public function photos()
    {
        return $this->morphMany(Media::class, 'model')
            ->where('collection_name', self::MEDIA_COLLECTION_PHOTOS);
    }

    /**
     * @return MorphMany
     */
    public function certs()
    {
        return $this->morphMany(Media::class, 'model')
            ->where('collection_name', self::MEDIA_COLLECTION_CERTS);
    }
}
