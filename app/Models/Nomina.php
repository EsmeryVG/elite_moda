<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    protected $table = 'nominas';

    protected $fillable = [
        'periodo_inicio', 'periodo_fin', 'fecha_pago',
        'total_general', 'estado', 'usuario_id',
    ];

    protected $casts = [
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
        'fecha_pago' => 'date',
        'total_general' => 'decimal:2',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleNomina::class, 'nomina_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}