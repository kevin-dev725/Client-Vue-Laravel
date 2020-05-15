<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\WithTestCoupon;

class CouponTest extends TestCase
{
    use DatabaseTransactions, WithFaker, WithTestCoupon;

    public function testCheckCoupon()
    {
        $this->postJson('/web-api/coupon/check', [
            'coupon_code' => $this->testCouponId
        ])
            ->assertSuccessful()
            ->assertJson([
                'id' => $this->testCouponId,
                'percent_off' => $this->testCoupon->percent_off,
                'duration' => $this->testCoupon->duration,
                'duration_in_months' => $this->testCoupon->duration_in_months,
                'name' => $this->testCoupon->name,
            ]);
    }

    public function testCheckCouponWithInvalidCode()
    {
        $this->postJson('/web-api/coupon/check', [
            'coupon_code' => 'invalid code'
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['coupon_code']);
    }
}
