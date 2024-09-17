<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class DemandeResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->formatDate($this->date),
            'montant' => (float)$this->montant,
            'motif'=>$this->when(!isEmpty($this->motif), $this->motif),
            'etat'=>$this->etat,
            'client' => new ClientResource($this->whenLoaded("client")),    
            'articles' => ArticleResource::collection($this->whenLoaded('articles')),
        ];
    }
}
