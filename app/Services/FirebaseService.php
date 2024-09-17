<?php

namespace App\Services;

use App\Services\Interfaces\DatabaseConnectorInterface;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;

class FirebaseService implements DatabaseConnectorInterface
{
    protected $firestore;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));
        $this->firestore = $factory->createFirestore();
    }

    public function archiveDebt(array $debtDetails): void
    {
        $collection = $this->firestore->database()->collection('archived_dettes');
        $collection->add($debtDetails);
    }

    public function restoreDebt(int $debtId): ?array
    {
        $collection = $this->firestore->database()->collection('archived_dettes');
        $document = $collection->document((string)$debtId)->snapshot();

        if ($document->exists()) {
            $debtData = $document->data();

            // Remove from Firebase
            $collection->document((string)$debtId)->delete();

            return $debtData;
        }

        return null;
    }

    public function restoreMultipleDebts(array $debtIds): array
    {
        $restoredDebts = [];

        foreach ($debtIds as $debtId) {
            $debt = $this->restoreDebt($debtId);
            if ($debt) {
                $restoredDebts[] = $debt;
            }
        }

        return $restoredDebts;
    }

}
