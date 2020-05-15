<?php

namespace App\Transformers;


use App\Review;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ReviewTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = ['client', 'user'];

    /**
     * @param Review $review
     * @return array
     */
    public function transform(Review $review)
    {
        return array_merge($review->attributesToArray(), [
            'service_date' => Carbon::parse($review->service_date)->toDateString()
        ]);
    }

    /**
     * @param Review $review
     * @return Item
     */
    public function includeClient(Review $review)
    {
        return $this->item($review->client, new ClientTransformer());
    }

    /**
     * @param Review $review
     * @return Item
     */
    public function includeUser(Review $review)
    {
        return $this->item($review->user, new UserTransformer());
    }
}