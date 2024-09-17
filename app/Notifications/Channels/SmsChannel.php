<?php

namespace App\Notifications\Channels;

use App\Services\Interfaces\SmsServiceInterface;

class SmsChannel
{
    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    public function send($notifiable, $notification)
    {
        if (method_exists($notification, 'toSms')) {
            $message = $notification->toSms($notifiable);
            $this->smsService->sendSms($notifiable->telephone, $message);
        }
    }
}
