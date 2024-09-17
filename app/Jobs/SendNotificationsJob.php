<?php

namespace App\Jobs;

use App\Models\User;
use App\Facades\RoleFacade;
use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\DemandeSoumiseNotification;

class SendNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(private Client $client) {}

    public function handle()
    {
        $boutiquiers = User::where('role_id', RoleFacade::getId("boutiquier"))->get();
        foreach ($boutiquiers as $boutiquier) {
            Log::info("Boutiquier {$boutiquier->id} send notification");
            $boutiquier->notify(new DemandeSoumiseNotification($this->client));
        }
    }
}
