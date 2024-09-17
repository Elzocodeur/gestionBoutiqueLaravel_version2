<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\DetteResource;
use App\Facades\DetteRepositoryFacade;
use App\Http\Resources\ClientResource;

class PaiementResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dette = $this->whenLoaded('dette');
        if ($dette instanceof \App\Models\Dette)
            $dette = DetteRepositoryFacade::loadMontantVerserAndRestant($dette);
        else
            $dette = null;
        return [
            'id' => $this->when($this->id != null, $this->id),
            'montant' => (float)$this->montant,
            'date' => $this->formatDate($this->date),
            'detteId' => $this->when($this->dette_id != null, $this->when(
                !$this->relationLoaded('dette'),
                $this->dette_id
            )),
            'clientId' => $this->when($this->client_id!=null,$this->when(
                !$this->relationLoaded('client'),
                $this->client_id
            )),
            'dette' => $this->when($dette, new DetteResource($dette)),
            'client' => new ClientResource($this->whenLoaded('client')),
        ];
    }
}
