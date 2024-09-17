<?php

namespace App\Http\Resources;

use App\Facades\RoleFacade;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'photo' => $this->is_photo_local? url($this->photo_url) : $this->photo_url,
            'email' => $this->email,
            'role' => $this->role->libelle ?? RoleFacade::getLibelle($this->role_id),
        ];
    }
}
