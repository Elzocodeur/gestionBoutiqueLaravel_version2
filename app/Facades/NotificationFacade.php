<?php

namespace App\Facades;

use App\Services\Interfaces\NotificationServiceInterface;
use Illuminate\Support\Facades\Facade;

class NotificationFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return NotificationServiceInterface::class;
    }
}
