<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Descuento;
use App\Models\VarianteProducto;

class DescuentoService
{
    /**
     * Busca el mejor descuento aplicable para una variante y cliente dados.
     * Compara descuento de producto, cliente directo y grupo de cliente,
     * y retorna el de mayor valor monetario.
     */
    public function mejorDescuento(VarianteProducto $variante, Cliente $cliente, float $precioUnitario): array
    {
        $candidatos = collect();

        // 1. Descuento por producto
        $descuentoProducto = Descuento::activos()->vigentes()
            ->whereHas('variantes', fn($q) => $q->where('variante_producto_id', $variante->id))
            ->whereNull('cliente_id')
            ->first();

        if ($descuentoProducto) {
            $candidatos->push([
                'descuento' => $descuentoProducto,
                'monto'     => $descuentoProducto->calcularDescuento($precioUnitario),
            ]);
        }

        // 2. Descuento por cliente directo
        if (!$cliente->es_default) {
            $descuentoCliente = Descuento::activos()->vigentes()
                ->where('cliente_id', $cliente->id)
                ->first();

            if ($descuentoCliente) {
                $candidatos->push([
                    'descuento' => $descuentoCliente,
                    'monto'     => $descuentoCliente->calcularDescuento($precioUnitario),
                ]);
            }
        }

        // 3. Descuento por grupo de cliente
        if ($cliente->grupo_cliente_id) {
            $descuentoGrupo = Descuento::activos()->vigentes()
                ->whereHas('gruposCliente', fn($q) => $q->where('grupo_cliente_id', $cliente->grupo_cliente_id))
                ->whereNull('cliente_id')
                ->first();

            if ($descuentoGrupo) {
                $candidatos->push([
                    'descuento' => $descuentoGrupo,
                    'monto'     => $descuentoGrupo->calcularDescuento($precioUnitario),
                ]);
            }
        }

        if ($candidatos->isEmpty()) {
            return ['descuento' => null, 'monto' => 0];
        }

        // Retorna el de mayor monto de descuento
        return $candidatos->sortByDesc('monto')->first();
    }
}