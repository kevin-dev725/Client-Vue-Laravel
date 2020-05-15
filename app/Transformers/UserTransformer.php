<?php

namespace App\Transformers;

use App\Plan;
use App\User;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
        'clients',
        'reviews',
        'company',
        'subscribed_plan',
        'stripe_subscription',
        'subscription',
        'client_imports'
    ];

    protected $defaultIncludes = ['country'];

    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        return array_merge($user->attributesToArray(), [
            'is_admin' => $user->isAdmin(),
            'is_user' => $user->isUser(),
            'is_subscribed_to_plan' => $user->subscribed(Plan::SUBSCRIPTION_MAIN),
            'full_street_address' => $user->getFullStreetAddress(),
            'reviews_submitted' => $user->reviews()->count(),
            'reviews_submitted_average' => $user->reviews()->avg('star_rating'),
            'finished_signup' => $user->finishedSignup(),
            'client_count' => $user->clients()->count(),
        ]);
    }

    /**
     * @param User $user
     * @return Collection
     */
    public function includeClients(User $user)
    {
        return $this->collection($user->clients, new ClientTransformer());
    }

    /**
     * @param User $user
     * @return Collection
     */
    public function includeReviews(User $user)
    {
        return $this->collection($user->reviews, new ReviewTransformer());
    }

    /**
     * @param User $user
     * @return Item
     */
    public function includeSubscribedPlan(User $user)
    {
        if ($plan = $user->getSubscribedPlan()) {
            return $this->item($plan, new PlanTransformer());
        }
        return null;
    }

    /**
     * @param User $user
     * @return Item
     */
    public function includeCompany(User $user)
    {
        if ($user->company) {
            return $this->item($user->company, new CompanyTransformer());
        }
        return null;
    }

    /**
     * @param User $user
     * @return Item|null
     */
    public function includeStripeSubscription(User $user)
    {
        if ($subscription = $user->subscription(Plan::SUBSCRIPTION_MAIN)) {
            return $this->item($subscription->asStripeSubscription(), new StripeSubscriptionTransformer());
        }
        return null;
    }

    /**
     * @param User $user
     * @return Item|null
     */
    public function includeSubscription(User $user)
    {
        if ($subscription = $user->subscription(Plan::SUBSCRIPTION_MAIN)) {
            return $this->item($subscription, new SubscriptionTransformer());
        }
        return null;
    }

    /**
     * @param User $user
     * @return Item|null
     */
    public function includeCountry(User $user)
    {
        if (!$user->country) {
            return null;
        }
        return $this->item($user->country, new CountryTransformer());
    }

    /**
     * @param User $user
     * @return Collection
     */
    public function includeClientImports(User $user)
    {
        return $this->collection($user->client_imports, new ClientImportTransformer());
    }
}
