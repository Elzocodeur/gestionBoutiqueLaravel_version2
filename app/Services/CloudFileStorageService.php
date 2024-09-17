<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Services\Interfaces\CloudFileStorageServiceInterface;

class CloudFileStorageService implements CloudFileStorageServiceInterface
{

    /**
     * Télécharge un fichier sur Cloudinary.
     *
     * @param  string  $filePath
     * @return string
     * @throws Exception
     */
    public function uploadFile(string $filePath): string
    {
        try {
            $uploadResult = Cloudinary::upload($filePath, [
                'folder' => 'uploads',
                'public_id' => uniqid() . '_' . basename($filePath)
            ]);

            Log::info("Uploading file from path: ", [$filePath]);
            Log::info('Cloudinary upload result:', [$uploadResult]);

            $securePath = $uploadResult->getSecurePath();
            if (!$securePath) {
                throw new Exception('Le chemin sécurisé du fichier est null.');
            }

            return $securePath;
        } catch (Exception $e) {
            Log::error("Erreur sur Cloudinary: " . $e->getMessage());
            throw new Exception('Erreur lors du téléchargement sur Cloudinary : ' . $e->getMessage());
        }
    }
}
