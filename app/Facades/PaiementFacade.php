<?php

namespace App\Facades;

use App\Services\Interfaces\PaiementServiceInterface;
use Illuminate\Support\Facades\Facade;

class PaiementFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PaiementServiceInterface::class;
    }
}
