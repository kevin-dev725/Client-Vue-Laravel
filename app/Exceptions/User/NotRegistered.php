<?php

namespace App\Exceptions\User;

use App\Exceptions\DynamicException;
use App\Exceptions\DynamicExceptionHelper;
use App\Exceptions\NonReportable;

class NotRegistered extends NonReportable implements DynamicException
{
    use DynamicExceptionHelper;
}
