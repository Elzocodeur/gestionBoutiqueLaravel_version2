<?php

namespace App\Services\Interfaces;

use Illuminate\Http\UploadedFile;

interface UserServiceInterface
{
    public function getAll(array $filters = []);

    public function findById(int $id);

    public function create(array $data, ?UploadedFile $photo = null);

    public function update(int $id, array $data, ?UploadedFile $photo = null);

    public function delete(int $id);

    public function search(array $criteria);

    public function createUserForClient(array $data);

    public function emailExist(string $email);

    public function findByColumn(string $column, string $value, string $condition = "=", bool $many = true);
}
