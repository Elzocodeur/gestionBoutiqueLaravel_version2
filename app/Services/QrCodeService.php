<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use App\Services\Interfaces\QrCodeServiceInterface;

class QrCodeService implements QrCodeServiceInterface
{
    /**
     * Generate a QR code.
     *
     * @param string $data
     * @return string The path to the generated QR code image.
     */
    public function generateQrCode(string $data): string
    {
        // Crée une instance de QrCode
        $qrCode = new QrCode($data);
        $writer = new PngWriter();

        // Crée le QR code et sauvegarde l'image
        $result = $writer->write($qrCode);
        $fileName = 'qrcodes/' . uniqid() . '.png';
        Storage::disk('public')->put($fileName, $result->getString());

        return 'storage/' . $fileName;
    }
}