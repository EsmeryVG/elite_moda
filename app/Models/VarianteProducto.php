<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VarianteProducto extends Model
{
    use HasFactory;

    protected $table = 'variante_productos';

    protected $fillable = [
    'codigo',
    'producto_id',
    'descripcion',
    'color',
    'talla',
    'material',
    'precio_venta',
];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}