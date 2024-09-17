<?php

namespace App\Services;

use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Support\Facades\Log;
use Infobip\Api\SmsApi;
use Infobip\ApiException;
use Infobip\Configuration;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;

class InfobipSmsService implements SmsServiceInterface
{
    protected $client;
    protected $sender;

    public function __construct()
    {
        $configuration = new Configuration(
            host: env('INFOBIP_API_URL'),
            apiKey: env('INFOBIP_API_KEY')
        );
        $this->client = new SmsApi(config: $configuration);
    }

    public function sendSms(string $phoneNumber, string $message): bool
    {
        $phoneNumber = "+221776795840";

        try {
            $messageText = new SmsTextualMessage(
                destinations: [
                    new SmsDestination(to: $phoneNumber)
                ],
                from: "BoutiqueODC",
                text: $message
            );

            $request = new SmsAdvancedTextualRequest(messages: [$messageText]);
            $smsResponse = $this->client->sendSmsMessage($request);
            return true;
        } catch (ApiException $e) {
            Log::info("Erreur lors de l'envoi de sms: " . $e->getMessage());
            return false;
        }
    }
}
