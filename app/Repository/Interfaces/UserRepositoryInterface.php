<?php

namespace App\Repository\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{

    public function all(array $filters = []): Collection;

    public function find(int $id): ?User;

    public function create(array $attributes): User;

    public function update(int $id, array $attributes): User;

    public function delete(int $id): bool;

    public function search(array $criteria): Collection;

    public function createUserForClient(array $userData, int $clientId);

    public function findByEmail(string $email);

    public function findByColumn(string $column, string $value, string $condition = "=", bool $many = true);

}