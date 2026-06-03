<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecepcionMercancia extends Model
{
    use HasFactory;

    protected $table = 'recepciones_mercancia';

    protected $fillable = [
        'orden_compra_id',
        'usuario_id',
        'codigo',
        'fecha',
        'tipo',
        'observaciones',
        'motivo_rechazo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleRecepcion::class, 'recepcion_id');
    }
}