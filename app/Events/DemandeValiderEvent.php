<?php

namespace App\Events;

use App\Models\Demande;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DemandeValiderEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    use Dispatchable, SerializesModels;

    public $demande;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Demande $demande
     */
    public function __construct(Demande $demande)
    {
        $this->demande = $demande;
    }
}
