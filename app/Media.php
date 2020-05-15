<?php

namespace App;

use Carbon\Carbon;
use DateTimeInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\Filesystem\Filesystem;
use Spatie\MediaLibrary\Helpers\File;
use Spatie\MediaLibrary\Models\Media as SpatieMedia;

/**
 * App\Media
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string|null $mime_type
 * @property string $disk
 * @property int $size
 * @property array $manipulations
 * @property array $custom_properties
 * @property array $responsive_images
 * @property int|null $order_column
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $url
 * @property string|null $url_expires_at
 * @property-read mixed $extension
 * @property-read mixed $human_readable_size
 * @property-read mixed $type
 * @property-read Collection|Media[] $model
 * @method static Builder|Media newModelQuery()
 * @method static Builder|Media newQuery()
 * @method static Builder|SpatieMedia ordered()
 * @method static Builder|Media query()
 * @mixin Eloquent
 */
class Media extends SpatieMedia
{
    /**
     * @inheritdoc
     */
    public function getTemporaryUrl(DateTimeInterface $expiration, string $conversionName = '', array $options = []): string
    {
        if ($this->disk !== 's3') {
            return $this->getFullUrl();
        }
        if ($this->url && Carbon::parse($this->url_expires_at)->gt(now())) {
            return $this->url;
        }
        $this->update([
            'url' => parent::getTemporaryUrl($expiration, $conversionName, $options),
            'url_expires_at' => $expiration
        ]);
        return $this->url;
    }

    /**
     * Update the file for media.
     * @param UploadedFile $newFile
     */
    public function updateFile(UploadedFile $newFile)
    {
        $new_filename = $newFile->getClientOriginalName();
        $new_filename = $this->sanitizeFileName($new_filename);
        $path = $newFile->path();

        $this->name = pathinfo($new_filename, PATHINFO_FILENAME);
        $this->file_name = $new_filename;
        $this->mime_type = File::getMimetype($path);
        $this->size = filesize($path);
        $this->save();

        /** @var Filesystem $file_system */
        $file_system = app(Filesystem::class);
        $file_system->copyToMediaLibrary($path, $this, false, $new_filename);
    }

    protected function sanitizeFileName(string $fileName): string
    {
        return str_replace(['#', '/', '\\'], '-', $fileName);
    }
}
