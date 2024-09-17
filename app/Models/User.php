<?php

namespace App\Models;

use App\Facades\RoleFacade;
use App\Traits\UsesAuthTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens, Notifiable;

    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'password',
        'photo_url',
        'is_blocked',
        'is_photo_local',
        'role_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'password',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'is_photo_local' => 'boolean',
        'password' => 'hashed',
    ];

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function scopeFilterByStatus(Builder $query, string $etat): Builder
    {
        if (strtolower($etat) === "oui") {
            return $query->where('is_blocked', true);
        }
        if (strtolower($etat) === "non") {
            return $query->where('is_blocked', false);
        }
        return $query;
    }

    public function scopeFilterByRole(Builder $query, int $role)
    {
        return $query->where("role_id", $role);
    }

    public function scopeFindByColumn(Builder $query, $column, $value, $condition = "=")
    {
        if ($column && $value) {
            return $query->where($column, $condition, $value);
        }
        return $query;
    }

    /**
     * Determine if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role_id === RoleFacade::getId('admin');
    }

    /**
     * Determine if the user is a client.
     *
     * @return bool
     */
    public function isClient(): bool
    {
        return $this->role_id === RoleFacade::getId('client');
    }

    /**
     * Determine if the user is a boutiquier (shopkeeper).
     *
     * @return bool
     */
    public function isBoutiquier(): bool
    {
        return $this->role_id === RoleFacade::getId('boutiquier');
    }
}
