<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'ordenes_compra';

    protected $fillable = [
        'proveedor_id',
        'almacen_id',
        'usuario_id',
        'codigo',
        'numero_factura',
        'fecha',
        'fecha_esperada',
        'subtotal',
        'impuesto',
        'total',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha'          => 'date',
        'fecha_esperada' => 'date',
        'subtotal'       => 'decimal:2',
        'impuesto'       => 'decimal:2',
        'total'          => 'decimal:2',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

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
        return $this->hasMany(DetalleOrdenCompra::class, 'orden_compra_id');
    }

    public function recepciones()
    {
        return $this->hasMany(RecepcionMercancia::class, 'orden_compra_id');
    }

    public function esCancelable(): bool
    {
        return !$this->recepciones()->exists();
    }
}