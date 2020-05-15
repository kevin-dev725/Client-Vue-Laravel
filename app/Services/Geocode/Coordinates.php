<?php

namespace App\Services\Geocode;

use App\Contracts\Geocode\Coordinates as CoordinatesContract;

class Coordinates implements CoordinatesContract
{
    private $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * {@inheritDoc}
     */
    public function getLat(): ?float
    {
        return array_get($this->response, 'lat');
    }

    /**
     * {@inheritDoc}
     */
    public function getLng(): ?float
    {
        return array_get($this->response, 'lng');
    }
}
