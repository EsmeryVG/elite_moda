<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_ventas';

    protected $fillable = [
        'venta_id',
        'variante_producto_id',
        'cantidad',
        'precio_unitario',
        'descuento_aplicado',
        'subtotal',
        'itbis_aplicado',
    ];

    protected $casts = [
        'precio_unitario'    => 'decimal:2',
        'descuento_aplicado' => 'decimal:2',
        'subtotal'           => 'decimal:2',
        'itbis_aplicado'     => 'boolean',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }
}