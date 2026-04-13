<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

  protected $fillable = [
    'codigo',
    'nombre',
    'descripcion',
    'marca',
    'categoria_id',
];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function variantes()
    {
        return $this->hasMany(VarianteProducto::class, 'producto_id');
    }
}