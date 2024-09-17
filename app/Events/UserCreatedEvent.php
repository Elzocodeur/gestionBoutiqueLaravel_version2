<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
 
    public function __construct(User $user)
    {
        $this->user = $user;
        Log::info("Event User, creating user", $user->toArray());

    }
}
