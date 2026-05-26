<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AtributoValor extends Model
{
    use HasFactory;

    protected $table = 'atributo_valores';

    protected $fillable = [
        'atributo_id',
        'valor',
        'orden',
        'estado',
    ];

    protected $casts = [
    'estado' => 'boolean',
    ];

    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'atributo_id');
    }

    public function variantes()
    {
        return $this->belongsToMany(
            VarianteProducto::class,
            'variante_valores',
            'atributo_valor_id',
            'variante_producto_id'
        );
    }
}