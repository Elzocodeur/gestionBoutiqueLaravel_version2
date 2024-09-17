<?php

namespace App\Services\Interfaces;

use Illuminate\Http\UploadedFile;

interface LocalFileStorageServiceInterface
{
    public function uploadFile(UploadedFile $file, $path="images"): string;
}
