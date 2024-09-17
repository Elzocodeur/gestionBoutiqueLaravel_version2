<?php

namespace App\Facades;

use App\Services\Interfaces\ClientServiceInterface;
use Illuminate\Support\Facades\Facade;

class ClientFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return ClientServiceInterface::class;
    }
}
