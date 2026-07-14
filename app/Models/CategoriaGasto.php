<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaGasto extends Model
{
    protected $table = 'categorias_gasto';

    protected $fillable = ['nombre', 'estado'];

    protected $casts = ['estado' => 'boolean'];

    public function gastos()
    {
        return $this->hasMany(Gasto::class, 'categoria_gasto_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }
}