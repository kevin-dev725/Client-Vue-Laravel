<?php

namespace App\Transformers;

use App\Country;
use League\Fractal\TransformerAbstract;

class CountryTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Country $country
     * @return array
     */
    public function transform(Country $country)
    {
        return [
            'id' => $country->id,
            'iso_3166_2' => $country->iso_3166_2,
            'name' => $country->name
        ];
    }
}
