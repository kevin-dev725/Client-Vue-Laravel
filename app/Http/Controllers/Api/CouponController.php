<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rules\StripeCouponCodeRule;
use App\Transformers\StripeCouponTransformer;
use Illuminate\Http\Request;
use Stripe\Coupon;

class CouponController extends Controller
{
    public function check(Request $request)
    {
        $this->validate($request, [
            'coupon_code' => [
                'required',
                'string',
                new StripeCouponCodeRule(),
            ]
        ]);
        return fractal(Coupon::retrieve($request->get('coupon_code')), new StripeCouponTransformer())
            ->respond();
    }
}
