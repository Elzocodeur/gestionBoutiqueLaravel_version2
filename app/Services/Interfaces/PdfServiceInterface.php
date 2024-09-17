<?php

namespace App\Services\Interfaces;

use App\Models\Client;
use App\Models\User;

interface PdfServiceInterface
{
    /**
     * Génère un PDF contenant les informations du client et le code QR.
     *
     * @param array $clientData Les données du client.
     * @param string $qrCodePath Le chemin vers l'image du code QR.
     * @return string Le chemin complet du fichier PDF généré.
     */
    public function generateClientPdf(Client|User $client, string $path = null,  string $template = "card"): string
    ;
}
