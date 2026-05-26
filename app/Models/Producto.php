<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'codigo',
        'nombre',
        'marca',
        'descripcion',
        'tiene_variantes',
        'estado',
    ];

    protected $casts = [
        'tiene_variantes' => 'boolean',
        'estado'          => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function variantes()
    {
        return $this->hasMany(VarianteProducto::class, 'producto_id');
    }

    public function varianteDefault()
    {
        return $this->hasOne(VarianteProducto::class, 'producto_id')
                    ->where('es_default', true);
    }

    public function atributos()
    {
        return $this->belongsToMany(
            Atributo::class,
            'producto_atributos',
            'producto_id',
            'atributo_id'
        );
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}