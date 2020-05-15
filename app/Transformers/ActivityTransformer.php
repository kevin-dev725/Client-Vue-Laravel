<?php

namespace App\Transformers;


use League\Fractal\TransformerAbstract;
use Spatie\Activitylog\Models\Activity;

class ActivityTransformer extends TransformerAbstract
{
    public function transform(Activity $activity)
    {
        return $activity->attributesToArray();
    }
}