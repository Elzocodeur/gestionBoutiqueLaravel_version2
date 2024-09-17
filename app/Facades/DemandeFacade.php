<?php

namespace App\Facades;

use App\Services\Interfaces\DemandeServiceInterface;
use Illuminate\Support\Facades\Facade;

class DemandeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DemandeServiceInterface::class;
    }
}
