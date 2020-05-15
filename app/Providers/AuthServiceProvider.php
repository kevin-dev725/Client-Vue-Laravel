<?php

namespace App\Providers;

use App\Client;
use App\Company;
use App\License;
use App\Media;
use App\Plan;
use App\Policies\ActivityPolicy;
use App\Policies\ClientPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\LicensePolicy;
use App\Policies\MediaPolicy;
use App\Policies\PlanPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\UserPolicy;
use App\Review;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Client::class => ClientPolicy::class,
        Review::class => ReviewPolicy::class,
        Company::class => CompanyPolicy::class,
        Plan::class => PlanPolicy::class,
        Activity::class => ActivityPolicy::class,
        Media::class => MediaPolicy::class,
        License::class => LicensePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /*Passport::routes(function ($router)
        {
            $router->all();
        }, [
            'prefix'     => 'api/v1',
            'middleware' => ['cors'],
        ]);*/

        Passport::tokensExpireIn(Carbon::now()
            ->addDays(30));
        Passport::refreshTokensExpireIn(Carbon::now()
            ->addDays(30));
    }
}
