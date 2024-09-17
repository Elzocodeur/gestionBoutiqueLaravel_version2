<?php

namespace App\Services\Interfaces;

use Illuminate\Http\UploadedFile;

interface CloudFileStorageServiceInterface
{
    public function uploadFile(string $file): string;
}
