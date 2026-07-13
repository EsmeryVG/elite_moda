<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'cliente_id',
        'empleado_id',
        'sesion_caja_id', 
        'almacen_id',
        'usuario_id',
        'codigo',
        'fecha',
        'subtotal',
        'descuento_total',
        'impuesto',
        'total',
        'estado',
        'ncf',
        'comprobante_fiscal_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha'           => 'datetime',
        'subtotal'        => 'decimal:2',
        'descuento_total' => 'decimal:2',
        'impuesto'        => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

        public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function comprobanteFiscal()
    {
        return $this->belongsTo(ComprobanteFiscal::class, 'comprobante_fiscal_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'venta_id');
    }

    public function totalPagado(): float
    {
        return (float) $this->pagos()->where('estado', 'confirmado')->sum('monto');
    }

    public function esAnulable(): bool
    {
        return $this->estado === 'completada';
    }

    public function sesionCaja()
    {
        return $this->belongsTo(SesionCaja::class, 'sesion_caja_id');
    }
}