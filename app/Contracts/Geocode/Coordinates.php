<?php

namespace App\Contracts\Geocode;

interface Coordinates
{
    public function getLat(): ?float;

    public function getLng(): ?float;
}
