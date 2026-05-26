<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VarianteProducto extends Model
{
    use HasFactory;

    protected $table = 'variante_productos';

    protected $fillable = [
        'producto_id',
        'codigo',
        'codigo_barras',
        'descripcion',
        'costo',
        'precio_venta',
        'descuento_maximo',
        'es_default',
        'estado',
    ];

    protected $casts = [
        'es_default' => 'boolean',
        'estado'     => 'boolean',
        'costo'      => 'decimal:2',
        'precio_venta' => 'decimal:2',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function valores()
    {
        return $this->belongsToMany(
            AtributoValor::class,
            'variante_valores',
            'variante_producto_id',
            'atributo_valor_id'
        );
    }

    public function stock()
    {
        return $this->hasMany(\App\Models\Stock::class, 'variante_producto_id');
    }
}