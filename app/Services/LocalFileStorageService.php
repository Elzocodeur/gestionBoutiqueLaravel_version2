<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\Interfaces\LocalFileStorageServiceInterface;

class LocalFileStorageService implements LocalFileStorageServiceInterface
{
    /**
     * Télécharge un fichier localement.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $path
     * @return string
     */
    public function uploadFile(UploadedFile $file, $path = "images"): string
    {
        $filePath = $file->store($path, 'public');
        return Storage::url($filePath);
    }
}