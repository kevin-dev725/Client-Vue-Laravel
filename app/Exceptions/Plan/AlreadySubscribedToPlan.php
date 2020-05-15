<?php

namespace App\Exceptions\Plan;

use App\Exceptions\DynamicException;
use App\Exceptions\DynamicExceptionHelper;
use Exception;

class AlreadySubscribedToPlan extends Exception implements DynamicException
{
    use DynamicExceptionHelper;
}
