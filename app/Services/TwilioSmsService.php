<?php

namespace App\Services;

use App\Services\Interfaces\SmsServiceInterface;
use Twilio\Rest\Client as TwilioClient;

class TwilioSmsService implements SmsServiceInterface
{

    protected $client;

    public function __construct()
    {
        $this->client = new TwilioClient(env('TWILIO_SID'), env('TWILIO_TOKEN'));
    }

    public function sendSms(string $phoneNumber, string $message): bool
    {
        $phoneNumber = "+221776795840";
        try {
            $this->client->messages->create($phoneNumber, [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => $message
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}
