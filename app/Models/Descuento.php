<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Descuento extends Model
{
    use HasFactory;

    protected $table = 'descuentos';

    protected $fillable = [
        'nombre',
        'tipo',
        'valor',
        'cliente_id',
        'fecha_inicio',
        'fecha_fin',
        'requiere_autorizacion',
        'estado',
    ];

    protected $casts = [
        'valor'                  => 'decimal:2',
        'fecha_inicio'           => 'date',
        'fecha_fin'              => 'date',
        'requiere_autorizacion'  => 'boolean',
        'estado'                 => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function variantes()
    {
        return $this->belongsToMany(
            VarianteProducto::class,
            'descuento_producto',
            'descuento_id',
            'variante_producto_id'
        );
    }

    public function gruposCliente()
    {
        return $this->belongsToMany(
            GrupoCliente::class,
            'descuento_grupo_cliente',
            'descuento_id',
            'grupo_cliente_id'
        );
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function scopeVigentes($query)
    {
        $hoy = now()->format('Y-m-d');
        return $query->where(function ($q) use ($hoy) {
            $q->whereNull('fecha_inicio')->orWhere('fecha_inicio', '<=', $hoy);
        })->where(function ($q) use ($hoy) {
            $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $hoy);
        });
    }

    public function esVigente(): bool
    {
        $hoy = now();
        $despuesDeInicio = !$this->fecha_inicio || $hoy->gte($this->fecha_inicio);
        $antesDeFin      = !$this->fecha_fin || $hoy->lte($this->fecha_fin);
        return $this->estado && $despuesDeInicio && $antesDeFin;
    }

    public function getAplicaAAttribute(): string
    {
        if ($this->cliente_id) return 'cliente';
        if ($this->variantes()->exists()) return 'producto';
        if ($this->gruposCliente()->exists()) return 'grupo_cliente';
        return 'sin_definir';
    }

    public function calcularDescuento(float $precio): float
    {
        if ($this->tipo === 'porcentaje') {
            return round($precio * ($this->valor / 100), 2);
        }
        return min($this->valor, $precio);
    }
}