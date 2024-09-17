<?php

namespace App\Repository;

use App\Models\Categorie;
use App\Repository\Interfaces\CategorieRepositoryInterface;

class CategorieRepository implements CategorieRepositoryInterface
{
    public function getCategorieLibelleById($id)
    {
        $categorie = Categorie::find($id);
        return $categorie ? $categorie->libelle : null;
    }

    public function getCategorieIdByLibelle($libelle)
    {
        $categorie = Categorie::where('libelle', $libelle)->first();
        return $categorie ? $categorie->id : null;
    }
}
