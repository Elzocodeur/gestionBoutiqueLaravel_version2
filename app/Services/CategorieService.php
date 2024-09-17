<?php

namespace App\Services;

use App\Repository\Interfaces\CategorieRepositoryInterface;
use App\Services\Interfaces\CategorieServiceInterface;

class CategorieService implements CategorieServiceInterface
{

    public function __construct(protected CategorieRepositoryInterface $categorieRepository) {}
    
    /**
     * Récupère le libellé du rôle par son ID.
     *
     * @param int $id
     * @return string|null
     */
    public function getLibelle($id)
    {
        return $this->categorieRepository->getCategorieLibelleById($id);
    }

    /**
     * Récupère l'ID du rôle par son libellé.
     *
     * @param string $libelle
     * @return int|null
     */
    public function getId($libelle)
    {
        return $this->categorieRepository->getCategorieIdByLibelle($libelle);
    }
}
