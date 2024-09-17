<?php

namespace App\Services;

use App\Repository\Interfaces\RoleRepositoryInterface;
use App\Services\Interfaces\RoleServiceInterface;

class RoleService implements RoleServiceInterface
{

    public function __construct(protected RoleRepositoryInterface $roleRepository) {}
    
    /**
     * Récupère le libellé du rôle par son ID.
     *
     * @param int $id
     * @return string|null
     */
    public function getLibelle($id)
    {
        return $this->roleRepository->getRoleLibelleById($id);
    }

    /**
     * Récupère l'ID du rôle par son libellé.
     *
     * @param string $libelle
     * @return int|null
     */
    public function getId($libelle)
    {
        return $this->roleRepository->getRoleIdByLibelle($libelle);
    }
}
