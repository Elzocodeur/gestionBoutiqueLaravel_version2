<?php

namespace App\Facades;

use App\Services\Interfaces\PdfServiceInterface;
use Illuminate\Support\Facades\Facade;

class PdfFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PdfServiceInterface::class;
    }
}
