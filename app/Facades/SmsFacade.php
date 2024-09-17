<?php

namespace App\Facades;

use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Support\Facades\Facade;

class SmsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SmsServiceInterface::class;
    }
}
