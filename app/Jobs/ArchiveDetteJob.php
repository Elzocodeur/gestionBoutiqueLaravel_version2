<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Facades\ArchiveDetteFacade;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class ArchiveDetteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        try {
            ArchiveDetteFacade::archiveDetteSolder();
        } catch (\Exception $e) {
            Log::error("Erreur dans ArchiveDetteJob: " . $e->getMessage());
        }
    }
}
