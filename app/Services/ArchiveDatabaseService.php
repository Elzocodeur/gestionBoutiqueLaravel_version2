<?php

namespace App\Services;

use App\Models\ArchiveDette;
use App\Models\Dette;
use App\Repository\FirebaseArchiveDetteRepository;
use App\Repository\MongoArchiveDetteRepository;
use App\Services\Interfaces\ArchiveDatabaseServiceInterface;
use Illuminate\Support\Facades\Log;

class ArchiveDatabaseService implements ArchiveDatabaseServiceInterface
{
    private FirebaseArchiveDetteRepository $firebaseRepository;
    private MongoArchiveDetteRepository $mongoRepository;

    public function __construct(
        FirebaseArchiveDetteRepository $firebaseRepository,
        MongoArchiveDetteRepository $mongoRepository
    ) {
        $this->firebaseRepository = $firebaseRepository;
        $this->mongoRepository = $mongoRepository;
    }

    /**
     * Archive a Dette instance in both Firebase and MongoDB.
     * 
     * @param Dette $dette
     * @return void
     */
    public function archive(Dette $dette): void
    {
        try {
            $this->firebaseRepository->archive($dette);
            $this->mongoRepository->archive($dette);
            Log::info("Dette archivée avec succès dans Firebase et MongoDB.");
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'archivage de la dette : " . $e->getMessage());
        }
    }

    /**
     * Get all archived Dette instances from both Firebase and MongoDB.
     * 
     * @return array
     */
    public function getAll(?string $clientId = null, ?\DateTime $date = null): array
    {
        $firebaseDettes = $this->firebaseRepository->getAll($clientId, $date);
        $mongoDettes = $this->mongoRepository->getAll($clientId, $date);
        return array_merge($firebaseDettes, $mongoDettes);
    }

    /**
     * Restore a Dette instance from either Firebase or MongoDB.
     * 
     * @param string $detteId
     * @return Dette|null
     */
    public function restore(string $detteId): ?Dette
    {
        $dette = $this->firebaseRepository->restore($detteId);
        if (!$dette) {
            $dette = $this->mongoRepository->restore($detteId);
        }
        return $dette;
    }

    /**
     * Delete a Dette instance in both Firebase and MongoDB.
     * 
     * @param int|string $detteId
     * @return void
     */
    public function delete(int|string $detteId): void
    {
        try {
            $this->firebaseRepository->delete($detteId);
            $this->mongoRepository->delete($detteId);
            Log::info("Dette supprimée avec succès de Firebase et MongoDB.");
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression de la dette : " . $e->getMessage());
        }
    }

    public function getById(int $id): ?Dette
    {
        $archiveDette =  $this->firebaseRepository->getById($id);
        if (!$archiveDette)
            $archiveDette = $this->mongoRepository->getById($id);
        if ($archiveDette) {
            return ArchiveDette::attach($archiveDette->toDette(), $archiveDette->articles, $archiveDette->paiements);
        }
        return null;
    }

    public function restoreByClient(int $clientId)
    {
        $detteRestaure = [];
        $dettes = $this->getAll($clientId);
        foreach ($dettes as $dette) {
            $detteRestaure[] = $this->restore($dette->id);
        }
        return $detteRestaure;
    }

    public function restoreByDate(\DateTime $date)
    {
        $detteRestaure = [];
        $dettes = $this->getAll(date: $date);
        foreach ($dettes as $dette) {
            $detteRestaure[] = $this->restore($dette->id);
        }
        return $detteRestaure;
    }
}
