<?php

namespace App\Facades;

use App\Services\Interfaces\ArchiveDatabaseServiceInterface;
use Illuminate\Support\Facades\Facade;

class ArchiveDatabaseFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return ArchiveDatabaseServiceInterface::class;
    }
}
