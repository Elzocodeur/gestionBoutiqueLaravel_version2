<?php

namespace App\Repository\Interfaces;

interface ClientRepositoryInterface
{

    public function getAll(array $filter = []);

    public function findById(int $id);

    public function createClient(array $clientData, $userData = null);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function searchByTelephone(string $telephone);

    public function findByUserId(int $userId);

    public function create(array $data);

    public function getHaveDette();
}
