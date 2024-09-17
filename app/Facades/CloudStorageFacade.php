<?php

namespace App\Facades;

use App\Services\Interfaces\CloudFileStorageServiceInterface;
use Illuminate\Support\Facades\Facade;

class CloudStorageFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return CloudFileStorageServiceInterface::class;
    }
}
