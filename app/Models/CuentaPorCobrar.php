<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CuentaPorCobrar extends Model
{
    use HasFactory;

    protected $table = 'cuentas_por_cobrar';

    protected $fillable = [
        'venta_id',
        'cliente_id',
        'codigo',
        'monto_total',
        'monto_pagado',
        'monto_pendiente',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
    ];

    protected $casts = [
        'monto_total'      => 'decimal:2',
        'monto_pagado'     => 'decimal:2',
        'monto_pendiente'  => 'decimal:2',
        'fecha_emision'    => 'date',
        'fecha_vencimiento'=> 'date',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function pagos()
    {
        return $this->hasMany(PagoCredito::class, 'cuenta_por_cobrar_id');
    }

    public function estaVencida(): bool
    {
        return $this->fecha_vencimiento->isPast()
            && !in_array($this->estado, ['pagada', 'anulada']);
    }
}