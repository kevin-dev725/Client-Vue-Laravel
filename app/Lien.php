<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Lien
 *
 * @property int $id
 * @property string $state
 * @property string $county
 * @property string|null $legal
 * @property string|null $lienor
 * @property string|null $owner
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|LienFile[] $files
 * @property-read int|null $files_count
 * @property-read array $file_urls
 * @method static Builder|Lien newModelQuery()
 * @method static Builder|Lien newQuery()
 * @method static Builder|Lien query()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|Lien onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|Lien withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Lien withoutTrashed()
 * @mixin Eloquent
 */
class Lien extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = [
        'deleted_at'
    ];

    protected $appends = [
        'file_urls'
    ];

    /**
     * @return HasMany
     */
    public function files()
    {
        return $this->hasMany(LienFile::class);
    }

    /**
     * Get the file urls for the lien record.
     *
     * @return array
     */
    public function getFileUrlsAttribute()
    {
        $url = rtrim(config('filesystems.disks.s3.url'), '/');
        $folder = trim(config('lien.files_folder'), '/');
        return $this->files()
            ->pluck('file_name')
            ->map(function ($file_name) use ($url, $folder) {
                return $url . '/' . $file_name;
            })
            ->values()
            ->toArray();
    }
}
