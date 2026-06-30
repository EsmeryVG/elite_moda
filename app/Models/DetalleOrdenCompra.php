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
        'cantidad_solicitada',
        'cantidad_recibida',
        'precio_unitario',
        'itbis_incluido',
        'itbis_porcentaje',
        'subtotal',
    ];

    protected $casts = [
        'precio_unitario'  => 'decimal:2',
        'itbis_incluido'   => 'boolean',
        'itbis_porcentaje' => 'decimal:2',
        'subtotal'         => 'decimal:2',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    public function recepciones()
    {
        return $this->hasMany(DetalleRecepcion::class, 'detalle_orden_id');
    }

    public function getPrecioBaseAttribute(): float
    {
        if (!$this->itbis_incluido) {
            return (float) $this->precio_unitario;
        }
        return round($this->precio_unitario / (1 + $this->itbis_porcentaje / 100), 2);
    }

    public function getItbisUnitarioAttribute(): float
    {
        if (!$this->itbis_incluido) {
            return 0;
        }
        return round($this->precio_unitario - $this->precio_base, 2);
    }

    public function getDescripcionAttribute(): string
    {
        $producto = $this->variante?->producto?->nombre ?? '—';
        $valores  = $this->variante?->valores->map(fn($v) =>
            $v->atributo?->nombre . ': ' . $v->valor
        )->join(', ');

        return $valores ? "$producto — $valores" : $producto;
    }
}