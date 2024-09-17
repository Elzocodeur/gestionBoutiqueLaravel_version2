<?php

namespace App\Facades;

use App\Repository\Interfaces\DetteRepositoryInterface;
use Illuminate\Support\Facades\Facade;

class DetteRepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DetteRepositoryInterface::class;
    }
}
