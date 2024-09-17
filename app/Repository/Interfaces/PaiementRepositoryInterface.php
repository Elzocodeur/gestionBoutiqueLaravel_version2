<?php

namespace App\Repository\Interfaces;

use App\Models\Paiement;

interface PaiementRepositoryInterface
{
    public function getAll($filter = []);
    public function getById($id, $filter = []);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findByDette($detteId);
    public function findByClient($clientId);
    public function filter(array $filters);
}
