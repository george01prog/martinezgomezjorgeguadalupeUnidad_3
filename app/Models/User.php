<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->role === self::ROLE_ADMIN ? 'Administrador' : 'Usuario';
    }

    public function scopeActiveAdmins(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_ADMIN)->where('is_active', true);
    }

    /** ¿Es este el único administrador activo del sistema? */
    public function isLastActiveAdmin(): bool
    {
        return $this->isAdmin()
            && $this->is_active
            && ! static::activeAdmins()->where('id', '!=', $this->id)->exists();
    }
}