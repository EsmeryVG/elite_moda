<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoPago extends Model
{
    use HasFactory;

    protected $table = 'tipos_pago';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'tipo_pago_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}