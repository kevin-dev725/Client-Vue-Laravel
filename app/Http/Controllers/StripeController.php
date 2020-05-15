<?php

namespace App\Http\Controllers;

use App\Notifications\User\SubscriptionDeleted;
use App\Notifications\User\SubscriptionUpdated;
use App\Notifications\User\SuccessfulSubscription;
use App\User;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Log;
use Symfony\Component\HttpFoundation\Response;

class StripeController extends WebhookController
{
    /**
     * @param $payload
     * @return Response
     */
    public function handleInvoicePaymentSucceeded(array $payload)
    {
        Log::info('', $payload);

        /**
         * @var User $user
         */
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        $user->notify(new SuccessfulSubscription());
        $user->invoice_models()
            ->create([
                'stripe_id' => $payload['data']['object']['id'],
                'amount' => round($payload['data']['object']['amount_paid'] / 100, 2),
            ]);
        activity('subscription')
            ->on($user)
            ->log("{$user->email} successfully paid their subscription for ($" . config('services.stripe.plan.price') . ").");

        return new Response('Invoice Payment Succeeded handled', 200);
    }

    public function handleCustomerSubscriptionUpdated(array $payload)
    {
        Log::info('', $payload);

        /**
         * @var User $user
         */
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        $object = $payload['data']['object'];
        $previousAttr = $payload['data']['previous_attributes'];

        if ($previousAttr['cancel_at_period_end'] && $previousAttr['canceled_at'] &&
            !$object['cancel_at_period_end'] && !$object['canceled_at']) {

            $user->notify(new SubscriptionUpdated('resume'));
            activity('subscription')
                ->on($user)
                ->log("{$user->email} successfully resumed their subscription.");
        }
        if (!$previousAttr['cancel_at_period_end'] && !$previousAttr['canceled_at'] &&
            $object['cancel_at_period_end'] && $object['canceled_at']) {

            $user->notify(new SubscriptionUpdated('cancel'));
            activity('subscription')
                ->on($user)
                ->log("{$user->email} successfully cancelled their subscription.");
        }

        return new Response('Customer Subscription Updated handled', 200);
    }

    /**
     * Handle a cancelled customer from a Stripe subscription.
     *
     * @param array $payload
     * @return Response
     */
    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $response = parent::handleCustomerSubscriptionDeleted($payload);

        /**
         * @var User $user
         */
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        $user->notify(new SubscriptionDeleted());
        activity('subscription')
            ->on($user)
            ->log("{$user->email}'s subscription has ended/cancelled.");

        return $response;
    }
}
