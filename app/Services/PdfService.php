<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Services\Interfaces\PdfServiceInterface;

class PdfService implements PdfServiceInterface
{
    /**
     * Génère un PDF contenant les informations du client et le code QR.
     *
     * @param Client $client Les données du client.
     * @return string Le chemin complet du fichier PDF généré.
     * @throws Exception Si une erreur survient lors de la génération ou de l'enregistrement du PDF.
     */
    public function generateClientPdf(Client|User $client, string $path = null,  string $template = "card"): string
    {
        try {
            $pathTemplate = "pdf.$template";
            // Génère le PDF à partir de la vue Blade
            $pdf = Pdf::loadView($pathTemplate, [
                'client' => $client,  // Passer l'objet Client à la vue Blade,
                'path' => $path
            ]);

            if (!$pdf) {
                throw new Exception('La génération du PDF a échoué.');
            }

            // Générer un nom unique pour le fichier PDF
            $pdfFileName = 'carte_fidelite_' . uniqid() . '.pdf';
            // Définir le chemin de stockage dans le répertoire 'storage/app/public/pdfs'
            $pdfFilePath = 'public/pdfs/' . $pdfFileName;

            // Enregistrer le fichier PDF dans le système de fichiers
            Storage::put($pdfFilePath, $pdf->output());

            // Retourner le chemin complet du fichier PDF généré
            return Storage::url($pdfFilePath);  // Utiliser 'url()' pour un accès public
        } catch (Exception $e) {
            // Gère les exceptions et renvoie un message d'erreur
            throw new Exception("Erreur lors de la génération du PDF: " . $e->getMessage());
        }
    }
}
