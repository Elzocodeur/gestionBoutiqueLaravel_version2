<?php

namespace App\Listeners;

use App\Events\UserCreatedEvent;
use App\Jobs\SendWelcomeEmailJob;


class SendWelcomeEmailListener
{

    public function handle(UserCreatedEvent $event): void
    {
        SendWelcomeEmailJob::dispatch($event->user);
    }
}
