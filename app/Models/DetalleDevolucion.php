<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleDevolucion extends Model
{
    protected $fillable = [
        'devolucion_id', 'variante_producto_id', 'detalle_venta_id',
        'cantidad', 'precio_unitario', 'subtotal',
        'motivo', 'condicion_inspeccion', 'itbis_linea',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'itbis_linea' => 'decimal:2',
    ];

    public function devolucion(): BelongsTo
    {
        return $this->belongsTo(Devolucion::class);
    }

    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    public function detalleVenta(): BelongsTo
    {
        return $this->belongsTo(DetalleVenta::class);
    }

    public function getResultadoSugeridoAttribute(): string
    {
        return in_array($this->motivo, ['defecto_fabrica', 'producto_danado'])
            ? 'no_conforme'
            : 'conforme';
    }
}