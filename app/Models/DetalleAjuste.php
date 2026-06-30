<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleAjuste extends Model
{
    use HasFactory;

    protected $table = 'detalle_ajustes';

    protected $fillable = [
        'ajuste_inventario_id',
        'variante_producto_id',
        'cantidad_sistema',
        'cantidad_real',
        'diferencia',
        'observacion',
    ];

    public function ajuste()
    {
        return $this->belongsTo(AjusteInventario::class, 'ajuste_inventario_id');
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }
}