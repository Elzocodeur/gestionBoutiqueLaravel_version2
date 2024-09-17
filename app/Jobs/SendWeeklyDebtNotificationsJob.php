<?php

namespace App\Jobs;

use App\Facades\ClientRepositoryFacade;
use App\Notifications\DebtSummaryNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Interfaces\SmsServiceInterface;

class SendWeeklyDebtNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $clients = ClientRepositoryFacade::getHaveDette();

        foreach ($clients as $client) {
            $totalDue = $client->currentDebt();
            // Log::info("Client id: {$client->id}, dette total: " . $totalDue);
            $client->notify(new DebtSummaryNotification($totalDue));
            $message = "Rappel : Vous avez un montant total de $totalDue dû pour cette semaine.";
            $this->sendSms($client->telephone, $message);
        }
    }

    protected function sendSms($phoneNumber, $message)
    {
        try {
            $phoneNumber = "+221778133537";
            $response = $this->smsService->sendSms($phoneNumber, $message);
            Log::info("SMS envoyé à $phoneNumber : $message");
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi du SMS à $phoneNumber : " . $e->getMessage());
        }
    }
}
