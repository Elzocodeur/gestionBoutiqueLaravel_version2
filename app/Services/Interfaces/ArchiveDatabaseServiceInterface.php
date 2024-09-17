<?php

namespace App\Services\Interfaces;

use App\Models\Dette;

interface ArchiveDatabaseServiceInterface
{
    public function archive(Dette $dette): void;

    public function getAll(?string $clientId = null, ?\DateTime $date = null): array;

    public function restore(string $detteId): ?Dette;

    public function delete(string|int $detteId): void;

    public function getById(int $id): ?Dette;

    public function restoreByClient(int $clientId);

    public function restoreByDate(\DateTime $date);
}
