<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientDetteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'surname' => $this->surname,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'dettes' => new DetteCollection($this->dettes),
        ];
    }
}
