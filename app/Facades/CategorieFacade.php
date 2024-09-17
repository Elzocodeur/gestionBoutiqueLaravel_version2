<?php

namespace App\Facades;

use App\Services\Interfaces\CategorieServiceInterface;
use Illuminate\Support\Facades\Facade;

class CategorieFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return CategorieServiceInterface::class;
    }
}
