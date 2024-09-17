<?php

namespace App\Facades;

use App\Services\Interfaces\LocalFileStorageServiceInterface;
use Illuminate\Support\Facades\Facade;

class LocalStorageFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return LocalFileStorageServiceInterface::class;
    }
}
