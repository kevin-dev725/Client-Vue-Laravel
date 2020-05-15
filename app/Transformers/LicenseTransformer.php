<?php

namespace App\Transformers;

use App\License;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class LicenseTransformer extends TransformerAbstract
{

    protected $defaultIncludes = ['photos', 'certs'];

    /**
     * A Fractal transformer.
     *
     * @param License $license
     * @return array
     */
    public function transform(License $license)
    {
        return $license->attributesToArray();
    }

    /**
     * @param License $license
     * @return Collection
     */
    public function includePhotos(License $license)
    {
        return $this->collection($license->photos, new MediaTransformer());
    }

    /**
     * @param License $license
     * @return Collection
     */
    public function includeCerts(License $license)
    {
        return $this->collection($license->certs, new MediaTransformer());
    }
}
