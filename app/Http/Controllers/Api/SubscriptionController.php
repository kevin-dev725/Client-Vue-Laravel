<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Subscription\SubscriptionAlreadyCancelled;
use App\Exceptions\Subscription\SubscriptionAlreadyResumed;
use App\Http\Controllers\Controller;
use App\Plan;
use App\Rules\StripeCouponCodeRule;
use App\User;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * @param Request $request
     * @throws SubscriptionAlreadyCancelled
     */
    public function cancel(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $request->user();
        $subscription = $user->subscription(Plan::SUBSCRIPTION_MAIN);
        if (!$subscription->active() || $subscription->onGracePeriod()) {
            throw new SubscriptionAlreadyCancelled();
        }
        transaction(function () use ($user, $subscription) {
            $subscription->cancel();
        });
    }

    /**
     * @param Request $request
     * @throws SubscriptionAlreadyResumed
     */
    public function resume(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $request->user();
        $subscription = $user->subscription(Plan::SUBSCRIPTION_MAIN);
        $this->validate($request, [
            'card_token' => [
                $subscription ? 'nullable' : 'required',
                'string'
            ],
            'plan_interval' => 'nullable|string|in:monthly,yearly',
            'coupon_code' => [
                'nullable',
                'string',
                new StripeCouponCodeRule(),
            ]
        ]);
        if (!$subscription) {
            //previously on trial
            $user->newSubscription(Plan::SUBSCRIPTION_MAIN, $request->get('plan_interval') === 'monthly' ? config('services.stripe.plan.id') : config('services.stripe.yearly_plan.id'))
                ->skipTrial()
                ->withCoupon($request->get('coupon_code'))
                ->create($request->get('card_token'));
            $user->update([
                'trial_ends_at' => now(),
            ]);
        } else {
            if ($subscription->active() && !$subscription->onGracePeriod()) {
                throw new SubscriptionAlreadyResumed();
            }
            if ($request->filled('card_token')) {
                $user->updateCard($request->get('card_token'));
            }
            transaction(function () use ($user, $subscription) {
                if ($subscription->onGracePeriod()) {
                    $subscription->resume();
                } else {
                    $user->newSubscription(Plan::SUBSCRIPTION_MAIN, config('services.stripe.plan.id'))
                        ->create();
                }
            });
        }
    }
}
