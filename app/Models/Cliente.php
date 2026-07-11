<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'grupo_cliente_id',
        'codigo',
        'cedula',
        'rnc',
        'nombre',
        'apellido',
        'telefono',
        'email',
        'direccion',
        'limite_credito',
        'balance_credito',
        'credito_activo',
        'es_default',
        'estado',
    ];

    protected $casts = [
        'credito_activo'  => 'boolean',
        'es_default'      => 'boolean',
        'estado'          => 'boolean',
        'limite_credito'  => 'decimal:2',
        'balance_credito' => 'decimal:2',
    ];

    public function grupo()
    {
        return $this->belongsTo(GrupoCliente::class, 'grupo_cliente_id');
    }

  //  public function ventas()
   // {
   //     return $this->hasMany(Venta::class, 'cliente_id');
   // }

   // public function cuentasPorCobrar()
   // {
   //     return $this->hasMany(CuentaPorCobrar::class, 'cliente_id');
   // }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . ($this->apellido ?? ''));
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function scopeNoDefault($query)
    {
        return $query->where('es_default', false);
    }

    public function cuentasPorCobrar()
{
    return $this->hasMany(CuentaPorCobrar::class, 'cliente_id');
}

public function tieneCredito(): bool
{
    return $this->credito_activo
        && $this->limite_credito > 0
        && ($this->balance_credito < $this->limite_credito);
}

public function getCreditoDisponibleAttribute(): float
{
    return max(0, (float) $this->limite_credito - (float) $this->balance_credito);
}
}