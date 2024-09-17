<?php

namespace App\Jobs;

use App\Facades\CloudStorageFacade;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UploadFileJob implements ShouldQueue
{
    use Queueable;

    protected $user;
    public $tries = 10;  
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    public function handle(): void
    {
        try {
            if ($this->user->is_photo_local) {
                $path = str_replace("storage", "public", $this->user->photo_url);
                $localPhotoPath = Storage::path($path);

                if (!Storage::exists($path)) {
                    throw new \Exception("Le fichier de la photo n'existe pas: {$localPhotoPath}");
                }

                $uploadedPhotoUrl = CloudStorageFacade::uploadFile($localPhotoPath);

                $this->user->update([
                    'photo_url' => $uploadedPhotoUrl,
                    'is_photo_local' => false,
                ]);

                // Log::info("Photo uploaded to cloud successfully for user: {$this->user->id}");
            } else {
                // Log::info("Le photo est déja été upload");
            }
        } catch (Throwable $e) {
            Log::error("Échec du chargement sur Cloudinary pour l'utilisateur {$this->user->id}: " . $e->getMessage());
            $this->release(600);
        }
    }

    public function failed(Exception $exception)
    {
        Log::error("Job failed after {$this->tries}");
    }
}
