<?php

namespace App\Exceptions\Subscription;

use App\Exceptions\DynamicException;
use App\Exceptions\DynamicExceptionHelper;
use App\Exceptions\NonReportable;

class SubscriptionAlreadyResumed extends NonReportable implements DynamicException
{
    use DynamicExceptionHelper;
}
