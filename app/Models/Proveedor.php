<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'codigo',
        'nombre',
        'rnc',
        'telefono',
        'email',
        'direccion',
        'contacto_nombre',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class, 'proveedor_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}  