<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Demande;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DemandePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isBoutiquier();  // Seuls les boutiquiers peuvent voir toutes les demandes
    }

    /**
     * Déterminer si l'utilisateur peut créer une nouvelle demande.
     */
    public function create(User $user)
    {
        return $user->isClient();  // Seuls les clients peuvent créer des demandes
    }

    /**
     * Déterminer si l'utilisateur peut relancer une demande.
     */
    public function relancer(User $user, Demande $demande)
    {
        Log::info("this is a", [Auth::user()->client->id,  $demande->client_id]);
        return $user->isClient() && $user->client->id === $demande->client_id;  // Seul le client peut relancer sa propre demande
    }

    /**
     * Déterminer si l'utilisateur peut changer l'état d'une demande.
     */
    public function changeEtat(User $user)
    {
        return $user->isBoutiquier();  // Seuls les boutiquiers peuvent changer l'état
    }

    /**
     * Déterminer si l'utilisateur peut afficher une demande spécif ique.
     */
    public function view(User $user, Demande $demande)
    {
        return $user->isBoutiquier();  // Seuls les boutiquiers peuvent voir une demande spécifique
    }

    /**
     * Déterminer si l'utilisateur peut voir les articles disponibles pour une demande.
     */
    public function disponible(User $user)
    {
        return $user->isBoutiquier();  // Seuls les boutiquiers peuvent voir la disponibilité des articles
    }

    /**
     * Avant toute vérification, vérifier si l'utilisateur est un client.
     */
    public function before(User $user)
    {
        if (!$user->isClient() && !$user->isBoutiquier()) {
            return false;  // Si l'utilisateur n'est ni client ni boutiquier, interdire toute action
        }
    }

    /**
     * Déterminer si l'utilisateur peut voir ses propres demandes.
     */
    public function viewOwnDemandes(User $user)
    {
        return $user->isClient();  // Seuls les clients peuvent voir leurs propres demandes
    }
}
