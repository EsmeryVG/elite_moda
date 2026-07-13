<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaCredito extends Model
{
    protected $table = 'notas_credito';

    protected $fillable = [
        'devolucion_id', 'cliente_id', 'codigo', 'ncf',
        'monto_original', 'monto_disponible', 'fecha', 'fecha_vencimiento', 'estado',
    ];

    protected $casts = [
        'monto_original' => 'decimal:2',
        'monto_disponible' => 'decimal:2',
        'fecha' => 'datetime',
        'fecha_vencimiento' => 'datetime',
    ];

    public function devolucion(): BelongsTo
    {
        return $this->belongsTo(Devolucion::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public static function generarCodigo(): string
    {
        $ultimo = static::orderByDesc('id')->first();
        $numero = $ultimo ? ((int) substr($ultimo->codigo, 3)) + 1 : 1;
        return 'NC-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function aplicarMonto(float $monto): void
    {
        $this->monto_disponible -= $monto;
        if ($this->monto_disponible <= 0) {
            $this->monto_disponible = 0;
            $this->estado = 'agotada';
        }
        $this->save();
    }
}