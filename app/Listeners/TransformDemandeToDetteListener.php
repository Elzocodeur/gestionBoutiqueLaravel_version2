<?php

namespace App\Listeners;

use App\Events\DemandeValiderEvent;
use App\Jobs\TransformDemandeToDetteJob;

class TransformDemandeToDetteListener
{
    public function handle(DemandeValiderEvent $event): void
    {
        TransformDemandeToDetteJob::dispatch($event->demande);
    }
}
