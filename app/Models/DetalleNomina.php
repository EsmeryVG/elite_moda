<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleNomina extends Model
{
    protected $table = 'detalle_nomina';

    protected $fillable = [
        'nomina_id', 'empleado_id', 'salario_base', 'total_comisiones', 'total_pagar',
    ];

    protected $casts = [
        'salario_base' => 'decimal:2',
        'total_comisiones' => 'decimal:2',
        'total_pagar' => 'decimal:2',
    ];

    public function nomina()
    {
        return $this->belongsTo(Nomina::class, 'nomina_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function comisiones()
    {
        return $this->hasMany(Comision::class, 'detalle_nomina_id');
    }
}