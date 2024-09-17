<?php

namespace App\Listeners;

use App\Events\UserCreatedEvent;
use App\Jobs\UploadFileJob;


class UploadFileListener
{

    public function handle(UserCreatedEvent $event): void
    {
        UploadFileJob::dispatch($event->user);
    }
}
