<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\Demande;
use App\Enums\DemandeEnum;
use App\Events\DemandeValiderEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\DemandeValideeNotification;
use App\Notifications\AnnulationValidationNotification;
use App\Notifications\DemandeNotification;

class SendNotificationProcessingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Client $client;

    public function __construct(
        private Demande $demande,
        private string $newEtat,
        private ?string $motif = null
    ) {
        $this->client = $demande->client;
    }


    public function handle()
    {
        Log::info("Send notification", [$this->demande, $this->newEtat]);
        if ($this->newEtat === DemandeEnum::ANNULER->value) {
            $this->client->notify(new AnnulationValidationNotification($this->motif));
            $message = "Votre demande à été annulé en raison de {$this->motif}";
            $this->demande->notify(new DemandeNotification($message));
        } elseif ($this->newEtat === DemandeEnum::VALIDER->value) {
            $this->client->notify(new DemandeValideeNotification());
            event(new DemandeValiderEvent($this->demande));
        }
    }
}

