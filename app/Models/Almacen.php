<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacenes';

    protected $fillable = [
        'sucursal_id',
        'nombre',
        'tipo',
        'direccion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'almacen_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}