<?php

namespace App\Transformers;


use App\Client;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ClientTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = ['reviews', 'company', 'user'];

    protected $defaultIncludes = ['country'];

    /**
     * @param Client $client
     * @return array
     */
    public function transform(Client $client)
    {
        return array_merge($client->attributesToArray(), [
            'avg_rating' => $client->getAvgRating() ?: 0,
            'review_count' => $client->all_reviews()->count(),
            'display_name' => $client->isOrganization() ? $client->organization_name : $client->name
        ]);
    }

    /**
     * @param Client $client
     * @return Collection
     */
    public function includeReviews(Client $client)
    {
        return $this->collection($client->all_reviews()->get(), new ReviewTransformer());
    }

    /**
     * @param Client $client
     * @return Item
     */
    public function includeCompany(Client $client)
    {
        return $this->item($client->company, new CompanyTransformer());
    }

    /**
     * @param Client $client
     * @return Item|null
     */
    public function includeCountry(Client $client)
    {
        if (!$client->country) {
            return null;
        }
        return $this->item($client->country, new CountryTransformer());
    }

    /**
     * @param Client $client
     * @return Item
     */
    public function includeUser(Client $client)
    {
        return $this->item($client->user, new UserTransformer());
    }
}