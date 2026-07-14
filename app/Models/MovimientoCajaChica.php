<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCajaChica extends Model
{
    protected $table = 'movimientos_caja_chica';

    protected $fillable = [
        'caja_chica_id', 'usuario_id', 'tipo', 'monto', 'concepto', 'fecha',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    public function cajaChica()
    {
        return $this->belongsTo(CajaChica::class, 'caja_chica_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}