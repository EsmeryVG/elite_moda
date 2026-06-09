<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleRecepcion extends Model
{
    use HasFactory;

    protected $table = 'detalle_recepciones';

    protected $fillable = [
        'recepcion_id',
        'detalle_orden_id',
        'variante_producto_id',
        'cantidad_recibida',
        'cantidad_aceptada',
        'estado_calidad',
        'observacion',
    ];

    public function recepcion()
    {
        return $this->belongsTo(RecepcionMercancia::class, 'recepcion_id');
    }

    public function detalleOrden()
    {
        return $this->belongsTo(DetalleOrdenCompra::class, 'detalle_orden_id');
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    public function estaAsociada(): bool
    {
        return !is_null($this->variante_producto_id);
    }
}