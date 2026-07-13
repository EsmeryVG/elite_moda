<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'cajas';

    protected $fillable = [
        'sucursal_id',
        'nombre',
        'almacen_id',
        'estado',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function sesiones()
    {
        return $this->hasMany(SesionCaja::class, 'caja_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopeDisponibles($query)
    {
        return $query->activas()
            ->whereDoesntHave('sesiones', fn ($q) => $q->where('estado', 'abierta'));
    }

    public function asignarAlmacenSiFalta(): void
    {
        if ($this->almacen_id) {
            return;
        }

        $almacenId = $this->calcularAlmacen();
        if ($almacenId) {
            $this->update(['almacen_id' => $almacenId]);
        }
    }

    private function calcularAlmacen(): ?int
    {
        $sucursal = $this->sucursal_id
            ? Sucursal::find($this->sucursal_id)
            : Sucursal::where('es_principal', true)->first();

        if ($sucursal) {
            $almacen = Almacen::where('estado', true)
                ->where('sucursal_id', $sucursal->id)
                ->where('tipo', 'secundario')
                ->first();

            if ($almacen) return $almacen->id;
        }

        $almacenSecundario = Almacen::where('estado', true)
            ->where('tipo', 'secundario')
            ->first();

        if ($almacenSecundario) return $almacenSecundario->id;

        return Almacen::where('estado', true)->first()?->id;
    }
}