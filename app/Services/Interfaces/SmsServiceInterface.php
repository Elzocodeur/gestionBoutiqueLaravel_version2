<?php

namespace App\Services\Interfaces;

interface SmsServiceInterface
{
    public function sendSms(string $phoneNumber, string $message): bool;
}