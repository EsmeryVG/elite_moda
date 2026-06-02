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
        'descuento_base',
        'descripcion',
    ];

    protected $casts = [
        'descuento_base' => 'decimal:2',
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'grupo_cliente_id');
    }
}