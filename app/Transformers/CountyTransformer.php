<?php

namespace App\Transformers;

use App\County;
use League\Fractal\TransformerAbstract;

class CountyTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param County $county
     * @return array
     */
    public function transform(County $county)
    {
        return $county->attributesToArray();
    }
}
