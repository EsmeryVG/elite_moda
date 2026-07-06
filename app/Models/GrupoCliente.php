<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GrupoCliente extends Model
{
    use HasFactory;

    protected $table = 'grupo_clientes';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'grupo_cliente_id');
    }

    public function descuentos()
    {
        return $this->belongsToMany(
            Descuento::class,
            'descuento_grupo_cliente',
            'grupo_cliente_id',
            'descuento_id'
        );
    }
}