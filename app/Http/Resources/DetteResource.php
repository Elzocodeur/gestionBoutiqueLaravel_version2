<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\PaiementResource;

class DetteResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dateArchive = 
            $this->archive_at 
            ? (
                $this->archive_at instanceof \DateTime 
                ? $this->formatDate($this->archive_at)
                : $this->formatDate(new \DateTime($this->archive_at))
            ) 
            : null;

        $echeance = 
            $this->echeance 
            ? (
                $this->echeance instanceof \DateTime 
                ? $this->formatDate($this->echeance) 
                : $this->formatDate(new \DateTime($this->echeance))
            ) 
            : null;

        return [
            'id' => $this->id,
            'date' => $this->formatDate($this->date),
            'echeance' => $this->when($this->echeance !== null, $echeance),
            'date_archive' => $this->when($this->archive_at !== null, $dateArchive),
            'montant' => (float)$this->montant,
            'montant_du' => (float)$this->montant_verser,
            'montant_restant' => (float)$this->montant_restant,
            'client' => new ClientResource($this->whenLoaded("client")),    
            'articles' => ArticleResource::collection($this->whenLoaded('articles')),
            'paiements' => PaiementResource::collection($this->whenLoaded('paiements')),
        ];
    }
}
