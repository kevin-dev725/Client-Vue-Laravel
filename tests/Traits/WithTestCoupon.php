<?php

namespace Tests\Traits;

use Exception;
use Stripe\Coupon;

trait WithTestCoupon
{
    protected $testCouponId = 'test-coupon';

    /**
     * @var Coupon
     */
    protected $testCoupon;

    public function setupTestCoupon()
    {
        try {
            $this->testCoupon = Coupon::retrieve($this->testCouponId);
        } catch (Exception $e) {
            $this->testCoupon = Coupon::create([
                'percent_off' => 20,
                'duration' => 'repeating',
                'duration_in_months' => 12,
                'id' => $this->testCouponId,
                'name' => sprintf('%s (20%% off)', 'Test Coupon')
            ]);
        }
    }
}
