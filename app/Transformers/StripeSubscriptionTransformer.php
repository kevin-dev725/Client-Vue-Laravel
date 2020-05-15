<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Stripe\Subscription;

class StripeSubscriptionTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Subscription $subscription
     * @return array
     */
    public function transform(Subscription $subscription)
    {
        return $subscription->jsonSerialize();
    }
}
