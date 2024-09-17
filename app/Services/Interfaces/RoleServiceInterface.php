<?php

namespace App\Services\Interfaces;

interface RoleServiceInterface
{

    public function getLibelle($id);

    public function getId($libelle);
}
