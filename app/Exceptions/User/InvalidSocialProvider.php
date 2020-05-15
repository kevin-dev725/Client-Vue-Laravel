<?php

namespace App\Exceptions\User;

use App\Exceptions\DynamicException;
use App\Exceptions\NonReportable;

class InvalidSocialProvider extends NonReportable implements DynamicException
{

    /**
     * Descriptive error code.
     * @return string
     */
    public function getDescriptiveErrorCode()
    {
        return 'invalid_social_provider';
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
        return self::RESPONSE_INTERNAL_SERVER_ERROR;
    }
}
