<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class User extends Authenticatable
{
    use HasFactory ,Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol_id',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'estado'            => 'boolean',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function esAdministrador(): bool
    {
        return $this->rol?->nombre === 'Administrador';
    }

    public function esCajero(): bool
    {
        return $this->rol?->nombre === 'Cajero';
    }

    public function esContable(): bool
    {
        return $this->rol?->nombre === 'Contable';
    }

    public function empleado()
    {
        return $this->hasOne(Empleado::class, 'user_id');
    }
    public function tienePermiso(string $clave): bool
{
    if ($this->esAdministrador()) {
        return true; // admin siempre tiene todo, sin excepción
    }

    return $this->rol?->permisos()->where('clave', $clave)->exists() ?? false;
}
}