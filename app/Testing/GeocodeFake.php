<?php

namespace App\Testing;

use App\Contracts\Geocode\Coordinates as CoordinatesContract;
use App\Contracts\Geocode\Geocode as GeocodeContract;
use App\Services\Geocode\Coordinates;

class GeocodeFake implements GeocodeContract
{
    /**
     * {@inheritDoc}
     */
    public function getCoordinatesForAddress($address): ?CoordinatesContract
    {
        return new Coordinates([
            'lat' => 51.2343564,
            'lng' => 4.4286108,
            'accuracy' => 'ROOFTOP',
            'formatted_address' => 'Samberstraat 69, 2060 Antwerpen, Belgium',
            'viewport' => [
                "northeast" => [
                    "lat" => 51.23570538029149,
                    "lng" => 4.429959780291502
                ],
                "southwest" => [
                    "lat" => 51.2330074197085,
                    "lng" => 4.427261819708497
                ]
            ]
        ]);
    }
}
