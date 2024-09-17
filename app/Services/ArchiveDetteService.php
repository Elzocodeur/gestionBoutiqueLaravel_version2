<?php

namespace App\Services;

use App\Models\Dette;
use Illuminate\Support\Facades\Log;
use App\Facades\DetteRepositoryFacade;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Interfaces\ArchiveDetteServiceInterface;
use App\Repository\Interfaces\ArchiveDetteRepositoryInterface;

class ArchiveDetteService implements ArchiveDetteServiceInterface
{
    private ArchiveDetteRepositoryInterface $repository;

    public function __construct(ArchiveDetteRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Archive a Dette instance.
     * 
     * @param Dette $dette
     * @return void
     */
    public function archive(Dette $dette): void
    {
        $this->repository->archive($dette);
    }

    /**
     * Restore a Dette instance from archive.
     * 
     * @param string $archiveId
     * @return Dette|null
     */
    public function restore(string $id): ?Dette
    {
        return $this->repository->restore($id);
    }

    /**
     * Delete a Dette instance by ID.
     * 
     * @param int $detteId
     * @return void
     */
    public function delete(int $detteId): void
    {
        $this->repository->delete($detteId);
    }

    /**
     * Create a new Dette instance.
     * 
     * @param Dette $dette
     * @return void
     */
    public function create(Dette $dette): void
    {
        $this->repository->create($dette);
    }

    /**
     * Get all dette records.
     * 
     * @return array
     */
    public function getAll(?string $clientId = null, ?\DateTime $date = null): array
    {
        return $this->repository->getAll($clientId, $date);
    }

    /**
     * Archive multiple Dette instances.
     * 
     * @param array $dettes
     * @return void
     */
    public function archiveMultiple(Collection $dettes): void
    {
        foreach ($dettes as $dette) {
            $this->archive($dette);
        }
    }

    /**
     * Restore multiple Dette instances from archive.
     * 
     * @param array $ids
     * @return array
     */
    public function restoreMultiple(array $ids): array
    {
        $restoredDettes = [];
        foreach ($ids as $id) {
            $dette = $this->restore($id);
            if ($dette !== null) {
                $restoredDettes[] = $dette;
            }
        }
        return $restoredDettes;
    }

    /**
     * Get archived Dette records by a specific date.
     * 
     * @param \DateTime $date
     * @return array
     */
    public function getArchivedByDate(\DateTime $date): array
    {
        return $this->repository->getArchivedByDate($date);
    }

    public function archiveDetteSolder()
    {
        $dettes = DetteRepositoryFacade::getDettePayer();
        Log::info("this is dette archiving", $dettes->toArray());
        $this->archiveMultiple($dettes);
    }
}
