<?php

namespace App\Exceptions;

trait DynamicExceptionHelper
{
    public function getDescriptiveErrorCode()
    {
        return snake_case(array_last(explode('\\', static::class)));
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return null;
    }

    /**
     * Response error code.
     * @return integer
     */
    public function getResponseCode()
    {
        return 403;
    }
}
