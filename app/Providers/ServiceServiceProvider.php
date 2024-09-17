<?php

namespace App\Providers;

use App\Services\InfobipSmsService;
use App\Services\SignalWireSmsService;
use App\Services\TwilioSmsService;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(\App\Services\Interfaces\RoleServiceInterface::class, \App\Services\RoleService::class);
        $this->app->bind(\App\Services\Interfaces\QrCodeServiceInterface::class, \App\Services\QrCodeService::class);
        $this->app->bind(\App\Services\Interfaces\ClientServiceInterface::class, \App\Services\ClientService::class);
        $this->app->bind(\App\Services\Interfaces\UserServiceInterface::class, \App\Services\UserService::class);
        $this->app->bind(\App\Services\Interfaces\CloudFileStorageServiceInterface::class, \App\Services\CloudFileStorageService::class);
        $this->app->bind(\App\Services\Interfaces\LocalFileStorageServiceInterface::class, \App\Services\LocalFileStorageService::class);
        $this->app->bind(\App\Services\Interfaces\DetteServiceInterface::class, \App\Services\DetteService::class);
        $this->app->bind(\App\Services\Interfaces\PaiementServiceInterface::class, \App\Services\PaiementService::class);
        $this->app->bind(\App\Services\Interfaces\ArticleServiceInterface::class, \App\Services\ArticleService::class);
        $this->app->bind(\App\Services\Interfaces\ArchiveDetteServiceInterface::class, \App\Services\ArchiveDetteService::class);
        $this->app->bind(\App\Services\Interfaces\ArchiveDatabaseServiceInterface::class, \App\Services\ArchiveDatabaseService::class);
        $this->app->bind(\App\Services\Interfaces\CategorieServiceInterface::class, \App\Services\CategorieService::class);
        $this->app->bind(\App\Services\Interfaces\DemandeServiceInterface::class, \App\Services\DemandeService::class);
        $this->app->bind(\App\Services\Interfaces\NotificationServiceInterface::class, \App\Services\NotificationService::class);
        $this->app->bind(\App\Services\Interfaces\PdfServiceInterface::class, \App\Services\PdfServiceV1::class);
        $this->app->bind(\App\Services\Interfaces\SmsServiceInterface::class, function ($app) {
            if (config("app.sms.driver") === "inforbip")
                return new InfobipSmsService();
            elseif (config("app.sms.driver") === "twilio")
                return new TwilioSmsService();
            return new SignalWireSmsService();
        });
    }


    public function boot(): void {}
}
