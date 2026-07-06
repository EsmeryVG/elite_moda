<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comision extends Model
{
    use HasFactory;

    protected $table = 'comisiones';

    protected $fillable = [
        'empleado_id',
        'venta_id',
        'monto_venta',
        'porcentaje_comision',
        'monto_comision',
        'fecha',
        'estado',
    ];

    protected $casts = [
        'monto_venta'         => 'decimal:2',
        'porcentaje_comision' => 'decimal:2',
        'monto_comision'      => 'decimal:2',
        'fecha'               => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}