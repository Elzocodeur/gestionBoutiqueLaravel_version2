<?php

namespace App\Services\Interfaces;

interface ClientServiceInterface
{
    
    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function find(int $id);

    public function getAllCLient($filters = []);

    public function searchByTelephone(string $telephone);

    public function findByUserId(int $userId);

    public function getClientWithUser(int $id);

    public function getClientWithDette(int $id);

}