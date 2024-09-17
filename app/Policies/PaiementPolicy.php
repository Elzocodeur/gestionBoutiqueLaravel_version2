<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\User;

class PaiementPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isBoutiquier();
    }


    public function view(User $user, Paiement $paiement): bool
    {
        return $user->isAdmin() 
            || $user->isBoutiquier() 
            || $user->id === $paiement->client_id;
    }


    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isBoutiquier();
    }


    public function update(User $user, Paiement $paiement): bool
    {
        return $user->isAdmin() 
            || $user->isBoutiquier() 
            || $user->id === $paiement->client_id; 
    }

    public function delete(User $user, Paiement $paiement): bool
    {
        return $user->isAdmin() 
            || $user->isBoutiquier() 
            || $user->id === $paiement->client_id; 
    }


    public function restore(User $user, Paiement $paiement): bool
    {
        return $user->isAdmin() || $user->isBoutiquier();
    }


    public function forceDelete(User $user, Paiement $paiement): bool
    {
        return $user->isAdmin() || $user->isBoutiquier();
    }
}
