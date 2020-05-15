<?php

namespace App\Transformers;

use App\Plan;
use League\Fractal\TransformerAbstract;

class PlanTransformer extends TransformerAbstract
{
    /**
     * @param Plan $plan
     * @return array
     */
    public function transform(Plan $plan)
    {
        return $plan->attributesToArray();
    }
}