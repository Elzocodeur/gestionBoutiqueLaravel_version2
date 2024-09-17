<?php

namespace App\Console\Commands;

use App\Jobs\ArchiveDetteJob;
use Illuminate\Console\Command;

class ArchiveDetteCommand extends Command
{
    protected $signature = 'dette:archive';
    protected $description = 'Archive old unpaid debts to MongoDB';

    public function handle()
    {
        ArchiveDetteJob::dispatch();
        $this->info('Debt archiving job dispatched successfully.');
    }
}
