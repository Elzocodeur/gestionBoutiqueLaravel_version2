<?php

namespace App\Models;

use App\Enums\DemandeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use InvalidArgumentException;

class Demande extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'montant',
        'date',
        'motif',
        'etat',
        'client_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'etat' => DemandeEnum::class,
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class)->withPivot(['quantity', 'price']);
    }

    public function setEtat(string $value): void
    {
        if (!in_array($value, DemandeEnum::cases(), true)) {
            throw new InvalidArgumentException("Invalid state value: $value");
        }

        $this->attributes['etat'] = $value;
    }

    public function getEtat(string $value): string
    {
        return $value;
    }

    public function scopeByEtat($query, string $etat)
    {
        return $query->where('etat', $etat);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByMinMontant($query, int $minMontant)
    {
        return $query->where('montant', '>=', $minMontant);
    }

    public function scopeByClientId($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }
}
