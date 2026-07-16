<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'venta_id', 'cliente_id', 'usuario_id', 'empleado_id', 'codigo',
        'fecha', 'total', 'motivo', 'estado',
        'requiere_autorizacion', 'autorizado_por', 'dias_desde_venta', 'incluye_itbis',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'requiere_autorizacion' => 'boolean',
        'incluye_itbis' => 'boolean',
        'total' => 'decimal:2',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function autorizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizado_por');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleDevolucion::class);
    }

    public function notaCredito(): HasOne
    {
        return $this->hasOne(NotaCredito::class);
    }

    public function getInspeccionCompletaAttribute(): bool
    {
        return $this->detalles->every(fn ($d) => $d->condicion_inspeccion !== 'pendiente');
    }
}