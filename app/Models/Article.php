<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'libelle',
        'price',
        'quantity',
        'seuil',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'price' => 'integer',
        'quantity' => 'integer',
    ];
    
    public function scopeAvailable(Builder $query, int $quantite): Builder
    {
        return $query->where('quantity', '>=', $quantite);
    }

    public function scopeIsDisponible(Builder $query, string $disponible): Builder
    {
        if ($disponible === 'oui') {
            return $query->where('quantity', '>', 0);
        } elseif ($disponible === 'non') {
            return $query->where('quantity', 0);
        }
        return $query;
    }


    public function scopeByLibelle(Builder $query, string $libelle): Builder
    {
        return $query->where('libelle', 'like', "%{$libelle}%");
    }
}
