<?php

namespace App\Repository\Interfaces;

use App\Models\Dette;
use App\Models\ArchiveDette;
use Illuminate\Database\Eloquent\Collection;

interface ArchiveDetteRepositoryInterface
{
    /**
     * Archive a Dette instance.
     * 
     * @param Dette $dette
     * @return void
     */
    public function archive(Dette $dette): void;

    /**
     * Restore a Dette instance from archive.
     * 
     * @param string $archiveId
     * @return Dette|null
     */
    public function restore(string $detteId): ?Dette;

    /**
     * Delete a Dette instance by ID.
     * 
     * @param int $detteId
     * @return void
     */
    public function delete(int|string $detteId): void;

    /**
     * Create a new Dette instance.
     * 
     * @param Dette $dette
     * @return void
     */
    public function create(Dette $dette): void;
    /**
     * Get all dette records.
     * 
     * @return array
     */
    // public function getAll(): array;
    public function getAll(?string $clientId = null, ?\DateTime $date = null): array;

    public function getById(int $detteId):?ArchiveDette;

    public function getArchivedByDate(\DateTime $date): array;

    public function restoreMultiple(array $ids): array;

    public function archiveMultiple(array|Collection $dettes): void;
}
