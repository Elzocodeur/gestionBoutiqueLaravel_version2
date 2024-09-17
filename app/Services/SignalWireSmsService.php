<?php

namespace App\Services;


use Exception;
use Illuminate\Support\Facades\Log;
use SignalWire\Rest\Client;
use App\Services\Interfaces\SmsServiceInterface;

class SignalWireSmsService implements SmsServiceInterface
{
    protected $client;
    protected $fromNumber;

    public function __construct()
    {
        $this->client = new Client(
            env('SIGNALWIRE_PROJECT_ID'),
            env('SIGNALWIRE_API_TOKEN'),
            ['signalwireSpaceUrl' => env('SIGNALWIRE_SPACE_URL')]
        );
        $this->fromNumber = env('SIGNALWIRE_FROM_NUMBER');
    }



    public function sendSms(string $phoneNumber, string $message): bool
    {
        try {
            $message = $this->client->messages->create(
                $this->fromNumber,
                [
                    'from' => $phoneNumber,
                    'body' => $message,
                ]
            );
            dd($message);
            return true;
        } catch (Exception $e) {
            Log::error("Failed to send SMS: " . $e->getMessage());
            return false;
        }
    }
}
