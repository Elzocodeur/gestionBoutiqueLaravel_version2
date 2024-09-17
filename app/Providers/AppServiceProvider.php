<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Notifications\ChannelManager;
use App\Notifications\Channels\SmsChannel;
use App\Services\Interfaces\SmsServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }


    public function boot(): void {
        $this->app[ChannelManager::class]->extend('sms', function ($app) {
            return new SmsChannel($app->make(SmsServiceInterface::class));
        });
    }
}
