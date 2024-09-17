<?php

namespace App\Services\Interfaces;

interface CategorieServiceInterface
{

    public function getLibelle($id);

    public function getId($libelle);
}
