<?php

namespace App\Policies;

use App\Models\Dette;
use App\Models\User;

class DettePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isBoutiquier();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dette $dette): bool
    {

        return $user->isAdmin() || $user->isBoutiquier()  || ($dette->client && $dette->client->user_id == $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isBoutiquier();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dette $dette): bool
    {
        return $user->isAdmin() || $user->isBoutiquier() || ($dette->client && $dette->client->user_id == $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dette $dette): bool
    {
        return $user->isAdmin() || $user->isBoutiquier();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Dette $dette): bool
    {
        return $user->isAdmin() || $user->isBoutiquier();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Dette $dette): bool
    {
        return $user->isAdmin() || $user->isBoutiquier();
    }
}
