<?php

namespace App\Observers;

use App\Models\User;
use App\Events\UserCreatedEvent;

class UserObserver
{

    public function created(User $user): void
    {
        event(new UserCreatedEvent($user));
    }


    public function updated(User $user): void
    {
        event(new UserCreatedEvent($user));
    }


    public function deleted(User $user): void
    {
    }


    public function restored(User $user): void
    {
    }


    public function forceDeleted(User $user): void
    {
    }
}
