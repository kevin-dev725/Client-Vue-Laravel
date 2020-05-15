<?php

namespace App\Transformers;

use App\Media;
use League\Fractal\TransformerAbstract;

class MediaTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Media $media
     * @return array
     */
    public function transform(Media $media)
    {
        return array_merge(
            $media->only('id', 'file_name', 'name', 'mime_type'),
            [
                'url' => $media->getTemporaryUrl(now()->addWeek()),
            ]
        );
    }
}
