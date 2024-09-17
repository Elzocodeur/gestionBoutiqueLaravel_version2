<?php

namespace App\Services\Interfaces;

use App\Models\Dette;

interface ArchiveDetteServiceInterface
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
    public function restore(string $archiveId): ?Dette;

    /**
     * Delete a Dette instance by ID.
     * 
     * @param int $detteId
     * @return void
     */
    public function delete(int $detteId): void;

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
    public function getAll(?string $clientId = null, ?\DateTime $date = null): array;

    public function archiveDetteSolder();

}
