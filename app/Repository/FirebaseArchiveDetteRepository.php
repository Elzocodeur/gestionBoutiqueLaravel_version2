<?php

namespace App\Repository;

use Kreait\Firebase\Database;
use App\Models\ArchiveDette;
use App\Models\Dette;
use Carbon\Carbon;
use App\Repository\Interfaces\ArchiveDetteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FirebaseArchiveDetteRepository implements ArchiveDetteRepositoryInterface
{
    private const ARCHIVES_PATH = 'archives';
    private const DETTES_PATH = 'dettes';

    public function __construct(
        private Database $database,
    ) {
        Log::info("Connexion réussie à firebase");
    }

    /**
     * Archive a Dette instance.
     * 
     * @param Dette $dette
     * @return void
     */
    public function archive(Dette $dette): void
    {
        $table = Carbon::now()->toDateString();
        $archive = ArchiveDette::create($dette);

        // Archive the dette in Firebase under the path with the current date
        $archiveId = $this->database
            ->getReference(self::ARCHIVES_PATH . '/' . $table)
            ->push($archive->toArray())
            ->getKey();
        $dette->delete();
    }


    public function restore(string $detteId): ?Dette
    {
        $archiveReference = $this->database->getReference(self::ARCHIVES_PATH);
        $archiveSnapshot = $archiveReference->getSnapshot();
        $archives = $archiveSnapshot->getValue();
        foreach ($archives as $archiveId => $archiveData) {
            foreach ($archiveData as $id => $archive) {
                $archive = new ArchiveDette($archive);

                if ($archive->containsDette($detteId)) {
                    $dette = $archive->toDette($detteId);
                    $data = $dette->toArray();
                    $data["echeance"] = now();
                    unset($data["archive_at"]);
                    $result = DB::table(self::DETTES_PATH)->updateOrInsert(
                        ['id' => $dette->id],
                        $data
                    );
                    if ($result) {
                        $dette = Dette::find($dette->id);
                        ArchiveDette::attach($dette, $archive->articles);
                    }
                    $this->database->getReference(self::ARCHIVES_PATH . '/' . $archiveId . '/' . $id)->remove();
                    return $dette;
                }
            }
        }
        return null;
    }

    public function getAll(?string $clientId = null, ?\DateTime $date = null): array
    {
        $archiveReference = $this->database->getReference(self::ARCHIVES_PATH);
        if ($date) {
            $archiveReference = $archiveReference->orderByKey()->equalTo($date->format('Y-m-d'));
        }
        $archiveSnapshot = $archiveReference->getSnapshot();
        $archives = $archiveSnapshot->getValue();
        $archivedDettes = [];

        if ($archives) {
            foreach ($archives as $archiveId => $archiveData) {
                foreach ($archiveData as $id => $archive) {
                    $archive = new ArchiveDette($archive);
                    $dette = $archive->toDette();
                    $dette->load("client");
                    // Filtrer par client si un clientId est fourni
                    if ($clientId && $dette->client->id !== (int)$clientId) {
                        continue;
                    }

                    $archivedDettes[] = $dette;
                }
            }
        }

        return $archivedDettes;
    }


    /**
     * Delete a Dette instance by ID.
     * 
     * @param int $detteId
     * @return void
     */
    public function delete(int|string $detteId): void
    {
        $this->database
            ->getReference(self::DETTES_PATH . '/' . $detteId)
            ->remove();
    }

    /**
     * Create a new Dette instance.
     * 
     * @param Dette $dette
     * @return void
     */
    public function create(Dette $dette): void
    {
        $this->database
            ->getReference(self::DETTES_PATH)
            ->push($dette->toArray());
    }

    /**
     * Get archived records by date.
     * 
     * @param \DateTime $date
     * @return array
     */
    public function getArchivedByDate(\DateTime $date): array
    {
        $path = self::ARCHIVES_PATH . '/' . $date->format('Y-m-d');
        $snapshot = $this->database->getReference($path)->getSnapshot();

        $data = [];
        foreach ($snapshot->getValue() ?? [] as $dette) {
            $data[] = (new ArchiveDette($dette))->toDette();
        }
        return $data;
    }

    /**
     * Restore multiple Dette instances by their IDs.
     * 
     * @param array $ids
     * @return array
     */
    public function restoreMultiple(array $ids): array
    {
        $restoredDettes = [];
        foreach ($ids as $id) {
            $dette = $this->restore($id);
            if ($dette) {
                $restoredDettes[] = $dette;
            }
        }
        return $restoredDettes;
    }

    /**
     * Archive multiple Dette instances.
     * 
     * @param array $dettes
     * @return void
     */
    public function archiveMultiple(Collection|array $dettes): void
    {
        foreach ($dettes as $dette) {
            if ($dette instanceof Dette)
                $this->archive($dette);
        }
    }

    /**
     * Get a Dette instance by its ID.
     * 
     * @param int $detteId
     * @return Dette|null
     */
    public function getById(int $detteId): ?ArchiveDette
    {

        $archiveReference = $this->database->getReference(self::ARCHIVES_PATH);
        $archiveSnapshot = $archiveReference->getSnapshot();
        $archives = $archiveSnapshot->getValue();
        if ($archives) {
            foreach ($archives as $archiveId => $archiveData) {
                foreach ($archiveData as $id => $archive) {
                    if ($archive["dette_id"] == $detteId) {
                        return new ArchiveDette($archive);
                    }
                }
            }
        }
        return null;
    }
}
