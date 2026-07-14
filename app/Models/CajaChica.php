<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaChica extends Model
{
    protected $table = 'caja_chica';

    protected $fillable = [
        'sucursal_id', 'nombre', 'monto_base', 'monto_disponible',
        'fecha_ultima_reposicion', 'estado',
    ];

    protected $casts = [
        'monto_base' => 'decimal:2',
        'monto_disponible' => 'decimal:2',
        'fecha_ultima_reposicion' => 'date',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoCajaChica::class, 'caja_chica_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function puedeReponerNormal(): bool
    {
        if ($this->monto_disponible >= $this->monto_base) {
            return false;
        }

        $diasReposicion = (int) Configuracion::get('caja_chica_dias_reposicion', 1);

        return $this->fecha_ultima_reposicion === null
            || $this->fecha_ultima_reposicion->diffInDays(now()) >= $diasReposicion;
    }
}