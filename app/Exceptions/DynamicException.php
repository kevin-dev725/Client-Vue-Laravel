<?php

namespace App\Exceptions;

interface DynamicException
{
    const RESPONSE_FORBIDDEN = 403, RESPONSE_SERVICE_UNAVAILABLE = 503, RESPONSE_INTERNAL_SERVER_ERROR = 500;

    /**
     * Descriptive error code.
     * @return string
     */
    public function getDescriptiveErrorCode();

    /**
     * @return array
     */
    public function getMetadata();

    /**
     * Response error code.
     * @return integer
     */
    public function getResponseCode();
}
