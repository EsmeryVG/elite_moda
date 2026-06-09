<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'variante_producto_id',
        'almacen_id',
        'cantidad_disponible',
        'cantidad_vendida',
        'cantidad_devuelta',
        'cantidad_mermada',
        'stock_minimo',
    ];

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function estaBajoMinimo(): bool
    {
        return $this->cantidad_disponible <= $this->stock_minimo;
    }

    public function estaAgotado(): bool
    {
        return $this->cantidad_disponible <= 0;
    }

    public function getNivelAttribute(): string
    {
        if ($this->estaAgotado())    return 'agotado';
        if ($this->estaBajoMinimo()) return 'bajo';
        return 'ok';
    }

    // Incrementar stock
    public static function incrementar(int $varianteId, int $almacenId, int $cantidad): self
    {
        $stock = self::firstOrCreate(
            ['variante_producto_id' => $varianteId, 'almacen_id' => $almacenId],
            ['cantidad_disponible' => 0, 'stock_minimo' => 5]
        );

        $stock->increment('cantidad_disponible', $cantidad);
        return $stock->fresh();
    }

    // Decrementar stock
    public static function decrementar(int $varianteId, int $almacenId, int $cantidad): self
    {
        $stock = self::firstOrCreate(
            ['variante_producto_id' => $varianteId, 'almacen_id' => $almacenId],
            ['cantidad_disponible' => 0, 'stock_minimo' => 5]
        );

        $stock->decrement('cantidad_disponible', $cantidad);
        return $stock->fresh();
    }
}