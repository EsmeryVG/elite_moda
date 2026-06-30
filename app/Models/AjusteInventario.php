<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AjusteInventario extends Model
{
    use HasFactory;

    protected $table = 'ajustes_inventario';

    protected $fillable = [
        'almacen_id',
        'usuario_id',
        'tipo',
        'motivo',
        'estado',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleAjuste::class, 'ajuste_inventario_id');
    }

    public function esPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }
}