<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    protected $fillable = [
        'codigo',
        'nombre',
        'direccion',
        'telefono',
        'es_principal',
        'estado',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'estado'       => 'boolean',
    ];

    public function almacenes()
    {
        return $this->hasMany(Almacen::class, 'sucursal_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }
}