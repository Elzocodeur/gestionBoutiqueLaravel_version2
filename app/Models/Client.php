<?php

namespace App\Models;

use App\Models\User;
use App\Models\Dette;
use App\Models\Demande;
use App\Models\Paiement;
use App\Models\Categorie;
use App\Facades\CategorieFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'surname',
        'adresse',
        'telephone',
        'qrcode',
        'max_montant',
        'user_id',
        'categorie_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'user_id' => 'integer',
        'categorie_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function scopeActive(Builder $query, $active)
    {
        $active = strtolower($active);
        if (!in_array($active, ["oui", "non"]))
            return $query;
        return $query->whereHas('user', function ($query) use ($active) {
            $query->where('is_blocked', $active !== 'oui');
        });
    }


    public function scopeHasAccount(Builder $query, $comptes)
    {
        if ($comptes === 'oui') {
            return $query->whereHas('user');
        } elseif ($comptes === 'non') {
            return $query->whereDoesntHave('user');
        }
        return $query;
    }

    /**
     * Determine if the user is an admin.
     *
     * @return bool
     */
    public function isBronze(): bool
    {
        return $this->categorie_id === CategorieFacade::getId('bronze');
    }

    /**
     * Determine if the user is a client.
     *
     * @return bool
     */
    public function isSilver(): bool
    {
        return $this->categorie_id === CategorieFacade::getId('silver');
    }

    /**
     * Determine if the user is a boutiquier (shopkeeper).
     *
     * @return bool
     */
    public function isGold(): bool
    {
        return $this->categorie_id === CategorieFacade::getId('gold');
    }

    public function hasDebt(): bool
    {
        return $this->dettes()->where(function ($query) {
            $query->whereRaw('montant > (SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.dette_id = dettes.id)');
        })->exists();
    }

    public function currentDebt(): int
    {
        return $this->dettes()->get()->sum(function ($dette) {
            $totalVerse = $dette->paiements()->sum('montant');
            return max(0, $dette->montant - $totalVerse);
        });
    }
}
