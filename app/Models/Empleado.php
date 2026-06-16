<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';

    protected $fillable = [
        'user_id',
        'codigo',
        'cedula',
        'nombre',
        'apellido',
        'telefono',
        'direccion',
        'cargo',
        'salario_base',
        'comision_porcentaje',
        'fecha_ingreso',
        'estado',
    ];

    protected $casts = [
        'fecha_ingreso'        => 'date',
        'salario_base'         => 'decimal:2',
        'comision_porcentaje'  => 'decimal:2',
        'estado'               => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}