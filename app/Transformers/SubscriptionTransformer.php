<?php

namespace App\Transformers;

use Laravel\Cashier\Subscription;
use League\Fractal\TransformerAbstract;

class SubscriptionTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Subscription $subscription
     * @return array
     */
    public function transform(Subscription $subscription)
    {
        return array_merge($subscription->attributesToArray(), [
            'is_on_grace_period' => $subscription->onGracePeriod(),
            'is_active' => $subscription->active(),
        ]);
    }
}
