<?php

namespace App\Jobs;

use App\Facades\DetteFacade;
use App\Facades\DetteRepositoryFacade;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaymentReminderNotification;

class SendPaymentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $dettes = DetteFacade::getNonSoldesEcheanceDepassee();
        foreach ($dettes as $dette) {
            $dette = DetteRepositoryFacade::loadMontantVerserAndRestant($dette);
            $client = $dette->client;
            if ($client && $client->phone_number) {
                $message = "Cher(e) client, votre paiement pour la dette du {$dette->echeance->format('d-m-Y')} est en retard. Merci de régler le montant dès que possible. Le montant restant est: {$dette->montant_restant}";
                $notification = new PaymentReminderNotification($message, $client->phone_number);
                Notification::send($client, $notification);
            }
        }
    }
}
