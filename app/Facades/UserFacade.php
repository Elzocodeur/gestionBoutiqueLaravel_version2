<?php

namespace App\Facades;

use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Facade;

class UserFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return UserServiceInterface::class;
    }
}
