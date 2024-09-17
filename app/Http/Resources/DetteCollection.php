<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\PaiementResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DetteCollection extends ResourceCollection
{

    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($dette) {
            $dateArchive = null;
            if (isset($dette->archve_at))
                $dateArchive =
                    $this->archive_at
                    ? (
                        $this->archive_at instanceof \DateTime
                        ? $this->formatDate($this->archive_at)
                        : $this->formatDate(new \DateTime($this->archive_at))
                    )
                    : null;
            $date =
                $dette->date instanceof \DateTime
                ? $this->formatDate($dette->date)
                : $this->formatDate(new \DateTime($dette->date));
            return [
                'id' => $dette->id,
                'date' => $date,
                'echeance' => $dette->echeance,
                'date_archive' => $this->when(isset($dette->archve_at) && $dette->archive_at !== null, $dateArchive),
                'montant' => (float)$dette->montant,
                'montant_du' => (float)$dette->montant_verser,
                'montant_restant' => (float)$dette->montant_restant,
                'articles' => ArticleResource::collection($dette->whenLoaded('articles')),
                'client' => new ClientResource($dette->whenLoaded('client')),
                'paiements' => PaiementResource::collection($dette->whenLoaded('paiements')),
            ];
        })->all();
    }

    protected function formatDate($date)
    {
        if ($date instanceof Carbon) {
            return $date->format('d-m-Y H:i');
        }
        return Carbon::parse($date)->format('d-m-Y H:i');
    }
}
