<?php

namespace App\Facades;

use App\Contracts\Geocode\Geocode as GeocodeContract;
use App\Testing\GeocodeFake;
use Illuminate\Support\Facades\Facade;

class Geocode extends Facade
{
    /**
     * Switch to fake geocode class.
     *
     * @return GeocodeFake
     */
    public static function fake()
    {
        static::swap($fake = new GeocodeFake());
        return $fake;
    }

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return GeocodeContract::class;
    }
}
