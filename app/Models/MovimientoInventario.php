<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'variante_producto_id',
        'almacen_id',
        'tipo',
        'cantidad',
        'referencia_tipo',
        'referencia_id',
        'motivo',
        'usuario_id',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public static function registrar(
        int    $varianteId,
        int    $almacenId,
        string $tipo,
        int    $cantidad,
        string $referenciaTipo = null,
        int    $referenciaId   = null,
        string $motivo         = null,
        int    $usuarioId      = null
    ): self {
        return self::create([
            'variante_producto_id' => $varianteId,
            'almacen_id'           => $almacenId,
            'tipo'                 => $tipo,
            'cantidad'             => $cantidad,
            'referencia_tipo'      => $referenciaTipo,
            'referencia_id'        => $referenciaId,
            'motivo'               => $motivo,
            'usuario_id'           => $usuarioId,
            'fecha'                => now(),
        ]);
    }
}