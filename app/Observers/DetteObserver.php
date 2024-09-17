<?php

namespace App\Observers;

use App\Exceptions\RepositoryException;
use App\Models\Dette;
use App\Models\Paiement;
use App\Repository\Interfaces\DetteRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetteObserver
{

    protected DetteRepositoryInterface $detteRepository;

    public function __construct(DetteRepositoryInterface $detteRepository)
    {
        $this->detteRepository = $detteRepository;
    }

    public function created(Dette $dette): void
    {
        $arclices = request()->only("articles");
        $paiement = request()->only("paiement");
        try {
            DB::beginTransaction();
            if ($arclices) {
                $this->detteRepository->attachArticles($dette, $arclices["articles"]);
            }
            if (count($paiement)) {
                $paiement = $paiement["paiement"];
                if(isset($paiement["montant"])){
                    $this->detteRepository->createPaiement($dette, [
                        'montant' => $paiement['montant'],
                        'date' => now(),
                        'dette_id' => $dette->id,
                        'client_id' => $dette->client_id
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            $dette->delete();
            DB::rollBack();
            throw new RepositoryException($e->getMessage());
        }
    }

    /**
     * Handle the Dette "updated" event.
     */
    public function updated(Dette $dette): void
    {
        //
    }

    /**
     * Handle the Dette "deleted" event.
     */
    public function deleted(Dette $dette): void
    {
        //
    }

    /**
     * Handle the Dette "restored" event.
     */
    public function restored(Dette $dette): void
    {
        //
    }

    /**
     * Handle the Dette "force deleted" event.
     */
    public function forceDeleted(Dette $dette): void
    {
        //
    }
}
