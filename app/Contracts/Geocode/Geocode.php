<?php

namespace App\Contracts\Geocode;

interface Geocode
{
    /**
     * Get coordinates from address.
     *
     * @param string $address
     * @return Coordinates|null
     */
    public function getCoordinatesForAddress($address): ?Coordinates;
}
