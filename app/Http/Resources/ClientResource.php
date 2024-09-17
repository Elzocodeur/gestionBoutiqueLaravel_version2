<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'surname' => $this->surname,
            'addrese' => $this->adresse,
            'telephone' => $this->telephone,
            'categorie' => $this->categorie->libelle,
            'max_montant' => $this->when($this->categorie->libelle == "silver", $this->max_montant),
            'qrcode' => url($this->qrcode),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
