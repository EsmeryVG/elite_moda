<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\AtributoValor;
use App\Models\VarianteProducto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductoVarianteService
{
    public function crearVarianteDinamica(Producto $producto, array $data): VarianteProducto
    {
        return DB::transaction(function () use ($producto, $data) {
            $atributoValorIds = $this->normalizarIds($data['atributo_valor_ids'] ?? []);

            if (count($atributoValorIds) === 0) {
                throw ValidationException::withMessages([
                    'atributo_valor_ids' => 'Debe seleccionar al menos un valor de atributo para crear la variante.',
                ]);
            }

            $this->validarValoresExistentes($atributoValorIds);
            $this->validarAtributosNoRepetidos($atributoValorIds);
            $this->validarCombinacionNoDuplicada($producto, $atributoValorIds);

            $variante = VarianteProducto::create([
                'producto_id' => $producto->id,
                'descripcion' => $data['descripcion'] ?? null,
                'precio_venta' => $data['precio_venta'],
            ]);

            $variante->codigo = 'VAR-' . str_pad($variante->id, 3, '0', STR_PAD_LEFT);
            $variante->save();

            $variante->valores()->sync($atributoValorIds);

            $atributoIds = AtributoValor::whereIn('id', $atributoValorIds)
                ->pluck('atributo_id')
                ->unique()
                ->values()
                ->toArray();

            $producto->atributos()->syncWithoutDetaching($atributoIds);

            return $variante;
        });
    }

    public function actualizarVarianteDinamica(VarianteProducto $variante, array $data): VarianteProducto
    {
        return DB::transaction(function () use ($variante, $data) {
            $atributoValorIds = $this->normalizarIds($data['atributo_valor_ids'] ?? []);

            if (count($atributoValorIds) === 0) {
                throw ValidationException::withMessages([
                    'atributo_valor_ids' => 'Debe seleccionar al menos un valor de atributo para actualizar la variante.',
                ]);
            }

            $this->validarValoresExistentes($atributoValorIds);
            $this->validarAtributosNoRepetidos($atributoValorIds);
            $this->validarCombinacionNoDuplicada($variante->producto, $atributoValorIds, $variante->id);

            $variante->update([
                'descripcion' => $data['descripcion'] ?? null,
                'precio_venta' => $data['precio_venta'],
            ]);

            $variante->valores()->sync($atributoValorIds);

            $atributoIds = AtributoValor::whereIn('id', $atributoValorIds)
                ->pluck('atributo_id')
                ->unique()
                ->values()
                ->toArray();

            $variante->producto->atributos()->syncWithoutDetaching($atributoIds);

            return $variante;
        });
    }

    private function normalizarIds(array $ids): array
    {
        return collect($ids)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();
    }

    private function validarValoresExistentes(array $atributoValorIds): void
    {
        $cantidadExistente = AtributoValor::whereIn('id', $atributoValorIds)->count();

        if ($cantidadExistente !== count($atributoValorIds)) {
            throw ValidationException::withMessages([
                'atributo_valor_ids' => 'Uno o más valores de atributo no existen.',
            ]);
        }
    }

    private function validarAtributosNoRepetidos(array $atributoValorIds): void
    {
        $atributoIds = AtributoValor::whereIn('id', $atributoValorIds)
            ->pluck('atributo_id');

        if ($atributoIds->count() !== $atributoIds->unique()->count()) {
            throw ValidationException::withMessages([
                'atributo_valor_ids' => 'No puedes seleccionar más de un valor para el mismo atributo.',
            ]);
        }
    }

    private function validarCombinacionNoDuplicada(Producto $producto, array $atributoValorIds, ?int $ignorarVarianteId = null): void
    {
        sort($atributoValorIds);

        $variantes = $producto->variantes()
            ->with('valores')
            ->when($ignorarVarianteId, function ($query) use ($ignorarVarianteId) {
                $query->where('id', '!=', $ignorarVarianteId);
            })
            ->get();

        foreach ($variantes as $variante) {
            $idsExistentes = $variante->valores
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values()
                ->toArray();

            if ($idsExistentes === $atributoValorIds) {
                throw ValidationException::withMessages([
                    'atributo_valor_ids' => 'Ya existe una variante con esa misma combinación de atributos.',
                ]);
            }
        }
    }
}