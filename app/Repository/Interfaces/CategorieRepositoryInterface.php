<?php

namespace App\Repository\Interfaces;

interface CategorieRepositoryInterface
{
    public function getCategorieLibelleById($id);

    public function getCategorieIdByLibelle($libelle);
}
