<?php

namespace App\Services\Geocode;

use App\Contracts\Geocode\Coordinates as CoordinatesContract;
use App\Contracts\Geocode\Geocode as GeocodeContract;
use Geocoder;

class Geocode implements GeocodeContract
{
    /**
     * {@inheritDoc}
     */
    public function getCoordinatesForAddress($address): ?CoordinatesContract
    {
        $response = Geocoder::getCoordinatesForAddress($address);
        if (!$response) {
            return null;
        }
        return new Coordinates($response);
    }
}
