<?php

namespace App\Services;

use App\Models\Almacen;
use App\Models\Producto;
use App\Models\AtributoValor;
use App\Models\Stock;
use App\Models\VarianteProducto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductoVarianteService
{
    // ── Inicializar stock en todos los almacenes activos ─

    private function inicializarStock(VarianteProducto $variante): void
    {
        $almacenes = Almacen::where('estado', true)->get();

        foreach ($almacenes as $almacen) {
            Stock::firstOrCreate(
                [
                    'variante_producto_id' => $variante->id,
                    'almacen_id'           => $almacen->id,
                ],
                [
                    'cantidad_disponible' => 0,
                    'cantidad_vendida'    => 0,
                    'cantidad_devuelta'   => 0,
                    'cantidad_mermada'    => 0,
                    'stock_minimo'        => 5,
                ]
            );
        }
    }

    // ── Crear variante individual (edición posterior) ────

    public function crearVarianteDinamica(Producto $producto, array $data): VarianteProducto
    {
        return DB::transaction(function () use ($producto, $data) {
            $atributoValorIds = $this->normalizarIds($data['atributo_valor_ids'] ?? []);

            if (count($atributoValorIds) === 0) {
                throw ValidationException::withMessages([
                    'atributo_valor_ids' => 'Debe seleccionar al menos un valor de atributo.',
                ]);
            }

            $this->validarValoresExistentes($atributoValorIds);
            $this->validarAtributosNoRepetidos($atributoValorIds);
            $this->validarCombinacionNoDuplicada($producto, $atributoValorIds);

            $variante = VarianteProducto::create([
                'producto_id'  => $producto->id,
                'descripcion'  => $data['descripcion'] ?? null,
                'costo'        => $data['costo'] ?? null,
                'precio_venta' => $data['precio_venta'],
                'es_default'   => false,
                'estado'       => true,
            ]);

            $variante->codigo = 'VAR-' . str_pad($variante->id, 3, '0', STR_PAD_LEFT);
            $variante->save();

            $variante->valores()->sync($atributoValorIds);
            $this->sincronizarAtributosProducto($producto, $atributoValorIds);
            $this->inicializarStock($variante);

            return $variante;
        });
    }

    // ── Crear producto simple (variante default) ─────────

    public function crearVarianteDefault(Producto $producto, array $data): VarianteProducto
    {
        return DB::transaction(function () use ($producto, $data) {
            $variante = VarianteProducto::create([
                'producto_id'  => $producto->id,
                'descripcion'  => $producto->nombre,
                'costo'        => $data['costo'] ?? null,
                'precio_venta' => $data['precio_venta'],
                'es_default'   => true,
                'estado'       => true,
            ]);

            $variante->codigo = 'VAR-' . str_pad($variante->id, 3, '0', STR_PAD_LEFT);
            $variante->save();

            $this->inicializarStock($variante);

            return $variante;
        });
    }

    // ── Crear múltiples variantes desde grilla ───────────

    public function crearVariantesMasivo(Producto $producto, array $variantes): void
    {
        DB::transaction(function () use ($producto, $variantes) {
            foreach ($variantes as $datos) {
                $atributoValorIds = $this->normalizarIds($datos['atributo_valor_ids'] ?? []);

                if (count($atributoValorIds) === 0) continue;

                $this->validarValoresExistentes($atributoValorIds);
                $this->validarAtributosNoRepetidos($atributoValorIds);
                $this->validarCombinacionNoDuplicada($producto, $atributoValorIds);

                $variante = VarianteProducto::create([
                    'producto_id'   => $producto->id,
                    'descripcion'   => $datos['descripcion'] ?? null,
                    'costo'         => $datos['costo'] ?? null,
                    'precio_venta'  => $datos['precio_venta'],
                    'codigo_barras' => !empty($datos['codigo_barras']) ? $datos['codigo_barras'] : null,
                    'es_default'    => false,
                    'estado'        => true,
                ]);

                $variante->codigo = 'VAR-' . str_pad($variante->id, 3, '0', STR_PAD_LEFT);
                $variante->save();

                $variante->valores()->sync($atributoValorIds);
                $this->sincronizarAtributosProducto($producto, $atributoValorIds);
                $this->inicializarStock($variante);
            }
        });
    }

    // ── Actualizar variante individual ───────────────────

    public function actualizarVarianteDinamica(VarianteProducto $variante, array $data): VarianteProducto
    {
        return DB::transaction(function () use ($variante, $data) {
            $atributoValorIds = $this->normalizarIds($data['atributo_valor_ids'] ?? []);

            if (!$variante->es_default && count($atributoValorIds) === 0) {
                throw ValidationException::withMessages([
                    'atributo_valor_ids' => 'Debe seleccionar al menos un valor de atributo.',
                ]);
            }

            if (!$variante->es_default) {
                $this->validarValoresExistentes($atributoValorIds);
                $this->validarAtributosNoRepetidos($atributoValorIds);
                $this->validarCombinacionNoDuplicada($variante->producto, $atributoValorIds, $variante->id);
            }

            $variante->update([
                'descripcion'   => $data['descripcion'] ?? null,
                'costo'         => $data['costo'] ?? null,
                'precio_venta'  => $data['precio_venta'],
                'codigo_barras' => $data['codigo_barras'] ?? null,
            ]);

            if (!$variante->es_default) {
                $variante->valores()->sync($atributoValorIds);
                $this->sincronizarAtributosProducto($variante->producto, $atributoValorIds);
            }

            // Al actualizar también asegurar que tiene stock en todos los almacenes
            // por si se agregó un almacén nuevo después de crear la variante
            $this->inicializarStock($variante);

            return $variante;
        });
    }

    // ── Helpers privados ─────────────────────────────────

    private function sincronizarAtributosProducto(Producto $producto, array $atributoValorIds): void
    {
        $atributoIds = AtributoValor::whereIn('id', $atributoValorIds)
            ->pluck('atributo_id')
            ->unique()
            ->values()
            ->toArray();

        $producto->atributos()->syncWithoutDetaching($atributoIds);
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
        $atributoIds = AtributoValor::whereIn('id', $atributoValorIds)->pluck('atributo_id');
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
            ->when($ignorarVarianteId, fn ($q) => $q->where('id', '!=', $ignorarVarianteId))
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