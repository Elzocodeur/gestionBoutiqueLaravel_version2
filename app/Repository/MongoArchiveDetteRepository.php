<?php

namespace App\Repository;

use Carbon\Carbon;
use App\Models\Dette;
use App\Models\ArchiveDette;
use Illuminate\Support\Facades\DB;
use MongoDB\Client;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use App\Repository\Interfaces\ArchiveDetteRepositoryInterface;

class MongoArchiveDetteRepository implements ArchiveDetteRepositoryInterface
{
    private const DATABASE = 'api_boutique';
    private const ARCHIVES_COLLECTION = 'archives';
    private const DETTES_PATH = 'dettes';
    private const DEFAULT_CONNECTION = 'mongodb+srv://apiboutiqueodc:khady6@cluster0.l0twm.mongodb.net/api_boutique';
    private Client $client;

    public function __construct()
    {
        $this->connect();
    }

    public function connect(): void
    {
        try {
            $this->client = new Client(env("# MONGO_DB_URI", self::DEFAULT_CONNECTION));
            Log::info("Connexion réussie à MongoDB");
        } catch (\Exception $e) {
            Log::info("Erreur de connexion à MongoDB : ", [$e->getMessage()]);
        }
    }

    /**
     * Archive a Dette instance.
     * 
     * @param Dette $dette
     * @return void
     */
    public function archive(Dette $dette): void
    {
        $archive = ArchiveDette::create($dette);
        $date = Carbon::now()->toDateString();
        $archiveCollectionName = self::ARCHIVES_COLLECTION . '_' . $date;
        $archiveCollection = $this->client->selectCollection(self::DATABASE, $archiveCollectionName);
        try {
            $result = $archiveCollection->insertOne($archive->toArray());
        } catch (\Exception $e) {
            Log::error('Error inserting archive: ' . $e->getMessage());
        }

        $dette->delete();
    }

    /**
     * Delete a Dette instance by ID.
     * 
     * @param string $detteId
     * @return void
     */
    public function delete(string|int $detteId): void
    {
        $detteCollection = $this->client->selectCollection(self::DATABASE, self::ARCHIVES_COLLECTION);
        $detteCollection->deleteOne(['dette_id' => $detteId]);
    }

    /**
     * Create a new Dette instance.
     * 
     * @param Dette $dette
     * @return void
     */
    public function create(Dette $dette): void
    {
        $detteCollection = $this->client->selectCollection(self::DATABASE, self::ARCHIVES_COLLECTION);
        $detteCollection->insertOne($dette->toArray());
    }


    public function getAll(?string $clientId = null, ?\DateTime $date = null): array
    {
        $archivedDettes = [];
        $collections = $this->client->selectDatabase(self::DATABASE)->listCollections();

        foreach ($collections as $collection) {
            $collectionName = $collection->getName();

            if ($date && $collectionName !== self::ARCHIVES_COLLECTION . '_' . $date->format('Y-m-d')) {
                continue;
            }

            $archiveCollection = $this->client->selectCollection(self::DATABASE, $collectionName);
            $filter = [];

            if ($clientId !== null) {
                $filter['client.id'] = (int) $clientId;
            }

            $cursor = $archiveCollection->find($filter);
            $archives = iterator_to_array($cursor, false);

            foreach ($archives as $archive) {
                $archiveArray = $this->toArray($archive);
                $archiveDette = new ArchiveDette($archiveArray);
                $dette = $archiveDette->toDette();
                $dette->load("client");
                $archivedDettes[] = $dette;
            }
        }

        return $archivedDettes;
    }


    /**
     * Restore a Dette instance from an archive by Dette ID.
     * 
     * @param string $detteId
     * @return Dette|null
     */
    public function restore(string $detteId): ?Dette
    {
        $collections = $this->client->selectDatabase(self::DATABASE)->listCollections();
        foreach ($collections as $collection) {
            $collectionName = $collection->getName();
            if (!str_starts_with($collectionName, self::ARCHIVES_COLLECTION)) {
                continue;
            }
            $archiveCollection = $this->client->selectCollection(self::DATABASE, $collectionName);
            $cursor = $archiveCollection->find();
            $archives = iterator_to_array($cursor, false);
            foreach ($archives as $archive) {
                $archiveArray = $this->toArray($archive);
                $archive = new ArchiveDette($archiveArray);
                if ($archive->containsDette($detteId)) {
                    $dette = $archive->toDette();
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
                    $archiveCollection->deleteOne(['_id' => $archiveArray["_id"]]);
                    return $dette;
                }
            }
        }

        return null;
    }


    private function toArray($data): array
    {
        if ($data instanceof BSONDocument) {
            return $this->convertBSONDocumentToArray($data);
        } elseif ($data instanceof BSONArray) {
            return $this->convertBSONArrayToArray($data);
        } else {
            return (array) $data;
        }
    }

    private function convertBSONDocumentToArray(BSONDocument $document): array
    {
        $array = $document->getArrayCopy();
        foreach ($array as &$value) {
            if ($value instanceof BSONDocument || $value instanceof BSONArray) {
                $value = $this->toArray($value);
            }
        }
        return $array;
    }

    private function convertBSONArrayToArray(BSONArray $array): array
    {
        $phpArray = [];
        foreach ($array as $value) {
            if ($value instanceof BSONDocument || $value instanceof BSONArray) {
                $phpArray[] = $this->toArray($value);
            } else {
                $phpArray[] = $value;
            }
        }
        return $phpArray;
    }


    public function getArchivedByDate(\DateTime $date): array
    {
        $archiveCollectionName = self::ARCHIVES_COLLECTION . '_' . $date->format('Y-m-d');
        $archiveCollection = $this->client->selectCollection(self::DATABASE, $archiveCollectionName);

        $cursor = $archiveCollection->find();
        $archives = iterator_to_array($cursor, false);

        // Mappe chaque document vers une instance de ArchiveDette et convertit en tableau
        return array_map(function ($document) {
            return (new ArchiveDette($this->toArray($document)))->toDette();
        }, $archives);
    }

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


    public function getById(int $detteId): ?ArchiveDette
    {
        $collections = $this->client->selectDatabase(self::DATABASE)->listCollections();
        foreach ($collections as $collection) {
            $collectionName = $collection->getName();
            if (!str_starts_with($collectionName, self::ARCHIVES_COLLECTION)) continue;
            $archiveCollection = $this->client->selectCollection(self::DATABASE, $collectionName);
            $cursor = $archiveCollection->find();
            $archives = iterator_to_array($cursor, false);
            foreach ($archives as $archive) {
                $archiveArray = $this->toArray($archive);
                $archive = new ArchiveDette($archiveArray);
                if ($archive->containsDette($detteId)) {
                    return $archive;
                }
            }
        }
        return null;
    }


    /**
     * Archive multiple Dette instances.
     * 
     * @param array $dettes
     * @return void
     */
    public function archiveMultiple(array|Collection $dettes): void
    {
        foreach ($dettes as $dette) {
            if ($dette instanceof Dette)
                $this->archive($dette);
        }
    }
}
