<?php

namespace App\Facades;

use App\Services\Interfaces\DetteServiceInterface;
use Illuminate\Support\Facades\Facade;

class DetteFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DetteServiceInterface::class;
    }
}
