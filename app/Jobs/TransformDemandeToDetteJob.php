<?php

namespace App\Jobs;

use App\Facades\DemandeFacade;
use App\Models\Demande;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransformDemandeToDetteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Demande $demande) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DemandeFacade::transformDemandeToDette($this->demande);
    }
}
