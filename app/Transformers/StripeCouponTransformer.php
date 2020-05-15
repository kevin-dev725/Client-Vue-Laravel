<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Stripe\Coupon;

class StripeCouponTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Coupon $coupon
     * @return array
     */
    public function transform(Coupon $coupon)
    {
        return $coupon->jsonSerialize();
    }
}
