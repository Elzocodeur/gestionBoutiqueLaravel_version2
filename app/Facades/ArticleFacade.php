<?php

namespace App\Facades;

use App\Services\Interfaces\ArticleServiceInterface;
use Illuminate\Support\Facades\Facade;

class ArticleFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ArticleServiceInterface::class;
    }
}
