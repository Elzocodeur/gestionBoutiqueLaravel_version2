<?php

namespace App\Providers;

use App\Services\Auth\AuthentificationPassportService;
use App\Services\Auth\AuthentificationSanctumSerivce;
use App\Services\Auth\Interfaces\AuthentificationServiceInterface;
use Illuminate\Support\ServiceProvider;

class AuthCustumServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(AuthentificationServiceInterface::class, function ($app) {
            if (env("AUTHENTICATION_DRIVER", "sanctum") === "passport") {
                return new AuthentificationPassportService();
            }
            return new AuthentificationSanctumSerivce();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
