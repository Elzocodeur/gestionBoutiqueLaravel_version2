<?php

namespace App\Repository;

use App\Repository\Interfaces\RoleRepositoryInterface;
use App\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{

    public function getRoleLibelleById($id)
    {
        $role = Role::find($id);
        return $role ? $role->libelle : null;
    }

    public function getRoleIdByLibelle($libelle)
    {
        $role = Role::where('libelle', $libelle)->first();
        return $role ? $role->id : null;
    }
}
