<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Dette;
use App\Models\User;
use App\Observers\ClientObserver;
use App\Observers\DetteObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot()
    {
        User::observe(UserObserver::class);
        Client::observe(ClientObserver::class);
        Dette::observe(DetteObserver::class);
    }
}
