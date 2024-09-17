<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\Interfaces\ArchiveDetteServiceInterface;

class ArchiveDetteFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return ArchiveDetteServiceInterface::class;
    }
}
