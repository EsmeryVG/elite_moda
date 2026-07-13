<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SesionCaja extends Model
{
    use HasFactory;

    protected $table = 'sesiones_caja';

        protected $fillable = [
        'caja_id',
        'monto_apertura',
        'fecha_apertura',
        'fecha_cierre',
        'monto_cierre_esperado',
        'monto_cierre_real',
        'diferencia',
        'observacion_diferencia',
        'usuario_apertura_id',
        'usuario_cierre_id',
        'revisada_por',
        'revisada_en',
        'estado',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'revisada_en' => 'datetime',
        'monto_apertura' => 'decimal:2',
        'monto_cierre_esperado' => 'decimal:2',
        'monto_cierre_real' => 'decimal:2',
        'diferencia' => 'decimal:2',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function usuarioApertura()
    {
        return $this->belongsTo(User::class, 'usuario_apertura_id');
    }

    public function usuarioCierre()
    {
        return $this->belongsTo(User::class, 'usuario_cierre_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'sesion_caja_id');
    }

    public function scopeAbiertas($query)
    {
        return $query->where('estado', 'abierta');
    }

    public function scopeDeUsuario($query, int $userId)
    {
        return $query->where('usuario_apertura_id', $userId);
    }

    public function revisadaPor()
    {
        return $this->belongsTo(User::class, 'revisada_por');
    }

    public function scopePendientesRevision($query)
    {
        return $query->where('estado', 'cerrada')
            ->where('diferencia', '!=', 0)
            ->whereNull('revisada_por');
    }

    public function calcularMontoEsperado(): float
    {
        $totalEfectivo = Pago::whereIn('venta_id', $this->ventas()->pluck('id'))
            ->whereHas('tipoPago', fn ($q) => $q->where('nombre', 'Efectivo'))
            ->where('estado', 'confirmado')
            ->sum('monto');

        return (float) $this->monto_apertura + (float) $totalEfectivo;
    }
}