<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Atributo extends Model
{
    use HasFactory;

    protected $table = 'atributos';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    public function valores()
    {
        return $this->hasMany(AtributoValor::class, 'atributo_id');
    }

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'producto_atributos',
            'atributo_id',
            'producto_id'
        );
    }
}