<?php

namespace App\Exceptions\User;

use App\Exceptions\DynamicException;
use Exception;

class ErrorUploadingAvatar extends Exception implements DynamicException
{

    /**
     * Descriptive error code.
     * @return string
     */
    public function getDescriptiveErrorCode()
    {
        return 'error_uploading_avatar';
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
