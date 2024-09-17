<?php

namespace App\Facades;

use App\Services\Interfaces\QrCodeServiceInterface;
use Illuminate\Support\Facades\Facade;

class QrCodeFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return QrCodeServiceInterface::class;
    }

}
