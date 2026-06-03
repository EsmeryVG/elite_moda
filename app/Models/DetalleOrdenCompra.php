<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleOrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'detalle_ordenes_compra';

    protected $fillable = [
        'orden_compra_id',
        'variante_producto_id',
        'caracteristicas_solicitadas',
        'cantidad_solicitada',
        'cantidad_recibida',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'caracteristicas_solicitadas' => 'array',
        'precio_unitario'             => 'decimal:2',
        'subtotal'                    => 'decimal:2',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    public function detallesRecepcion()
    {
        return $this->hasMany(DetalleRecepcion::class, 'detalle_orden_id');
    }

    public function esPorCaracteristicas(): bool
    {
        return is_null($this->variante_producto_id);
    }

    public function getDescripcionAttribute(): string
    {
        if ($this->variante_producto_id && $this->variante) {
            $combinacion = $this->variante->valores
                ->map(fn($v) => $v->atributo?->nombre . ': ' . $v->valor)
                ->join(' / ');

            return $this->variante->producto?->nombre
                . ($combinacion ? ' — ' . $combinacion : '');
        }

        if ($this->caracteristicas_solicitadas) {
            return $this->caracteristicas_solicitadas['descripcion'] ?? 'Sin descripción';
        }

        return '—';
    }
}