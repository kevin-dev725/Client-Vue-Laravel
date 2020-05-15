<?php

namespace App\Providers;

use App\Client;
use App\Contracts\Geocode\Geocode as GeocodeContract;
use App\Observers\ClientObserver;
use App\Observers\ReviewObserver;
use App\Observers\UserObserver;
use App\Review;
use App\Services\Geocode\Geocode;
use App\Services\HumanNameParser;
use App\Services\Quickbooks;
use App\User;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        User::observe(UserObserver::class);
        Client::observe(ClientObserver::class);
        Review::observe(ReviewObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }
        config([
            'services.facebook.redirect' => url('/social/facebook/callback'),
            'services.twitter.redirect' => url('/social/twitter/callback'),
            'services.linkedin.redirect' => url('/social/linkedin/callback'),
            'services.google.redirect' => url('/social/google/callback'),
            'services.quickbooks.redirect' => url('/quickbooks/callback'),
        ]);
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->app->singleton(Quickbooks::class, function ($app) {
            return new Quickbooks();
        });
        $this->app->bind(HumanNameParser::class, function ($app) {
            return new HumanNameParser();
        });
        $this->app->singleton(GeocodeContract::class, function () {
            return new Geocode();
        });
    }
}
