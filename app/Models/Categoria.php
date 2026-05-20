<?php

namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;

    class Categoria extends Model
    {
        use HasFactory;

        protected $table = 'categorias';

        protected $fillable = [
            'codigo',
            'nombre',
            'descripcion',
            'estado',
        ];

        protected $casts = [
        'estado' => 'boolean',
    ];

            public function productos()
        {
            return $this->hasMany(Producto::class, 'categoria_id');
        }

            public function scopeActivas($query)
        {
            return $query->where('estado', true);
        }

    }