<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdministrador(): bool
    {
        return $this->role === 'admin';
    }

    public function isUsuario(): bool
    {
        return $this->role === 'usuario';
    }

    public function hasRol(string $rol): bool
    {
        return $this->role === $rol;
    }

    public function scopeAdministradores($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeUsuarios($query)
    {
        return $query->where('role', 'usuario');
    }

    public function getRolNombreAttribute(): string
    {
        return $this->role ?? 'Sin rol';
    }

    public function isGoogleUser(): bool
    {
        return !is_null($this->google_id);
    }
}