<?php

namespace App\Jobs;

use App\Facades\CategorieFacade;
use Exception;
use App\Models\User;
use App\Facades\PdfFacade;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeEmailJob implements ShouldQueue
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
        $userClient = $this->user;
        $client = $this->user->client;
        $pathQrcode = "";
        $template = "card";
        if ($client) {
            $localPhotoPath = Storage::path(str_replace("storage", "public", $client->qrcode));
            $userClient = $client;
            $template = CategorieFacade::getLibelle($userClient->categorie_id);
        }
        $pathQrcode = PdfFacade::generateClientPdf($userClient, $localPhotoPath, $template);
        Mail::to($this->user->email)->send(new WelcomeEmail($userClient, $pathQrcode));
        Log::info("Welcome email with PDF sent to user: {$this->user->id}");
    }

    public function failed(Exception $exception)
    {
        Log::error("Job failed after {$this->tries}");
    }
}
