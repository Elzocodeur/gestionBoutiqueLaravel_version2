<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',
        'date',
        'echeance',
        'client_id',
        "montant_verser",
        "montant_restant",
        "archive_at",
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'montant' => 'integer',
        'date' => 'date',
        'echeance' => 'date',
        'client_id' => 'integer',   
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class)->withPivot(['quantity', 'price']);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
