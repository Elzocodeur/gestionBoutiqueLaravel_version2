<?php

namespace App\Services\Interfaces;

interface QrCodeServiceInterface
{
    public function generateQrCode(string $data): string;
}
