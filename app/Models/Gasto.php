<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gastos';

    protected $fillable = [
        'categoria_gasto_id', 'origen', 'nombre', 'monto',
        'usuario_id', 'fecha', 'metodo_pago', 'referencia',
        'es_reposicion_extraordinaria', 'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'datetime',
        'es_reposicion_extraordinaria' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaGasto::class, 'categoria_gasto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function scopeCajaChica($query)
    {
        return $query->where('origen', 'caja_chica');
    }

    public function scopeDirectos($query)
    {
        return $query->where('origen', 'directo');
    }
}