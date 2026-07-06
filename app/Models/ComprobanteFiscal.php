<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComprobanteFiscal extends Model
{
    use HasFactory;

    protected $table = 'comprobantes_fiscales';

    protected $fillable = [
        'tipo_comprobante',
        'prefijo_ncf',
        'rango_inicio',
        'rango_fin',
        'numero_actual',
        'fecha_vencimiento',
        'estado',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'estado'            => 'boolean',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'comprobante_fiscal_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function tieneDisponibilidad(): bool
    {
        return $this->numero_actual <= $this->rango_fin
            && $this->fecha_vencimiento->isFuture()
            && $this->estado;
    }

    public function getCantidadDisponibleAttribute(): int
    {
        return max(0, $this->rango_fin - $this->numero_actual + 1);
    }

    public function getPorcentajeUsoAttribute(): float
    {
        $total = $this->rango_fin - $this->rango_inicio + 1;
        $usados = $this->numero_actual - $this->rango_inicio;
        return $total > 0 ? round(($usados / $total) * 100, 1) : 0;
    }

    public function siguienteNumero(): string
    {
        $numero = str_pad($this->numero_actual, 8, '0', STR_PAD_LEFT);
        return $this->prefijo_ncf . $numero;
    }
}