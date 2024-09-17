<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendPaymentReminderJob;

class EnvoyerRappelsPaiementQuotidiens extends Command
{
    protected $signature = 'rappels:paiement';
    protected $description = 'Envoie des rappels de paiement quotidiens aux clients avec des dettes échues';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        dispatch(new SendPaymentReminderJob());
        $this->info('Les rappels de paiement quotidiens ont été envoyés.');
    }
}
