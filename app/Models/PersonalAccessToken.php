<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'abilities',
        'tokenable_id',
        'tokenable_type',
        'token',
        'created_at',
        'updated_at',
    ];
}
