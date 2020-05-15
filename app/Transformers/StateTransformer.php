<?php

namespace App\Transformers;

use App\State;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class StateTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'counties'
    ];

    /**
     * A Fractal transformer.
     *
     * @param State $state
     * @return array
     */
    public function transform(State $state)
    {
        return $state->attributesToArray();
    }

    /**
     * @param State $state
     * @return Collection
     */
    public function includeCounties(State $state)
    {
        return $this->collection($state->counties, new CountyTransformer());
    }
}
