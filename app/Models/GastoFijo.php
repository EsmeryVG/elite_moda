<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastoFijo extends Model
{
    protected $table = 'gastos_fijos';
    protected $fillable = ['nombre', 'categoria_gasto_id', 'monto_sugerido', 'estado'];
    protected $casts = ['estado' => 'boolean', 'monto_sugerido' => 'decimal:2'];

    public function categoria() { return $this->belongsTo(CategoriaGasto::class, 'categoria_gasto_id'); }
    public function gastos() { return $this->hasMany(Gasto::class, 'gasto_fijo_id'); }
    public function scopeActivos($q) { return $q->where('estado', true); }

    public function gastoDelMesActual()
    {
        return $this->gastos()->where('periodo', now()->format('Y-m'))->first();
    }
}