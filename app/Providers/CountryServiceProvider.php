<?php

namespace App\Providers;

use App\Country;
use Webpatser\Countries\CountriesServiceProvider;

class CountryServiceProvider extends CountriesServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function registerCountries()
    {
        $this->app->bind('countries', function ($app) {
            return new Country();
        });
    }
}
