<?php

namespace App\Repository\Interfaces;

interface RoleRepositoryInterface
{

    public function getRoleLibelleById($id);

    public function getRoleIdByLibelle($libelle);

}
