<?php

namespace App\Http\Controllers;

use App\Models\AjusteInventario;
use App\Models\Almacen;
use App\Models\DetalleAjuste;
use App\Models\MovimientoInventario;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AjusteInventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = AjusteInventario::with(['almacen', 'usuario'])
                                 ->orderBy('created_at', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $query->where('motivo', 'like', '%' . $request->buscar . '%');
        }

        $ajustes   = $query->paginate(10)->withQueryString();
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();

        if ($request->ajax()) {
            return view('ajustes._tabla', compact('ajustes'))->render();
        }

        return view('ajustes.index', compact('ajustes', 'almacenes'));
    }

    public function create()
    {
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();

        return view('ajustes.create', compact('almacenes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'almacen_id'                   => 'required|exists:almacenes,id',
            'tipo'                         => 'required|in:conteo_fisico,merma,daño,correccion,otro',
            'motivo'                        => 'nullable|string|max:255',
            'fecha'                         => 'required|date',
            'lineas'                        => 'required|array|min:1',
            'lineas.*.variante_id'          => 'required|exists:variante_productos,id',
            'lineas.*.cantidad_real'        => 'required|integer|min:0',
            'lineas.*.observacion'          => 'nullable|string',
        ], [
            'almacen_id.required' => 'El almacén es obligatorio.',
            'tipo.required'       => 'El tipo de ajuste es obligatorio.',
            'lineas.required'     => 'Debes agregar al menos una línea.',
        ]);

        DB::transaction(function () use ($request) {
            $ajuste = AjusteInventario::create([
                'almacen_id' => $request->almacen_id,
                'usuario_id' => Auth::id() ?? 1,
                'tipo'       => $request->tipo,
                'motivo'     => $request->motivo,
                'estado'     => 'pendiente',
                'fecha'      => $request->fecha,
            ]);

            foreach ($request->lineas as $linea) {
                $stock = Stock::where('variante_producto_id', $linea['variante_id'])
                            ->where('almacen_id', $request->almacen_id)
                            ->first();

                $cantidadSistema = $stock?->cantidad_disponible ?? 0;
                $cantidadReal    = (int) $linea['cantidad_real'];
                $diferencia      = $cantidadReal - $cantidadSistema;

                // Mermas y daños solo pueden disminuir stock, nunca aumentarlo
                if (in_array($request->tipo, ['merma', 'daño']) && $diferencia > 0) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'lineas' => 'Para ajustes de merma o daño, la cantidad real no puede ser mayor a la cantidad en sistema.',
                    ]);
                }

                DetalleAjuste::create([
                    'ajuste_inventario_id' => $ajuste->id,
                    'variante_producto_id' => $linea['variante_id'],
                    'cantidad_sistema'     => $cantidadSistema,
                    'cantidad_real'        => $cantidadReal,
                    'diferencia'           => $diferencia,
                    'observacion'          => $linea['observacion'] ?? null,
                ]);
            }
        });

        return redirect()->route('ajustes.index')
            ->with('success', 'Ajuste de inventario registrado, pendiente de aprobación.');
    }

    public function show(AjusteInventario $ajuste)
    {
        $ajuste->load([
            'almacen.sucursal',
            'usuario',
            'detalles.variante.producto',
            'detalles.variante.valores.atributo',
        ]);

        return view('ajustes.show', compact('ajuste'));
    }

    public function aprobar(AjusteInventario $ajuste)
    {
        if (!$ajuste->esPendiente()) {
            return redirect()->route('ajustes.show', $ajuste)
                ->with('error', 'Este ajuste ya fue procesado.');
        }

        DB::transaction(function () use ($ajuste) {
            $ajuste->load('detalles');

            foreach ($ajuste->detalles as $detalle) {
                if ($detalle->diferencia == 0) {
                    continue;
                }

                $stock = Stock::firstOrCreate(
                    [
                        'variante_producto_id' => $detalle->variante_producto_id,
                        'almacen_id'           => $ajuste->almacen_id,
                    ],
                    ['cantidad_disponible' => 0, 'stock_minimo' => 5]
                );

                $stock->update(['cantidad_disponible' => $detalle->cantidad_real]);

                MovimientoInventario::registrar(
                    varianteId:     $detalle->variante_producto_id,
                    almacenId:      $ajuste->almacen_id,
                    tipo:           $detalle->diferencia > 0 ? 'ajuste_positivo' : 'ajuste_negativo',
                    cantidad:       abs($detalle->diferencia),
                    referenciaTipo: 'ajuste_inventario',
                    referenciaId:   $ajuste->id,
                    motivo:         'Ajuste aprobado — ' . ($ajuste->motivo ?? ucfirst($ajuste->tipo)),
                    usuarioId:      Auth::id() ?? 1
                );
            }

            $ajuste->update(['estado' => 'aprobado']);
        });

        return redirect()->route('ajustes.show', $ajuste)
            ->with('success', 'Ajuste aprobado. El stock ha sido actualizado.');
    }

    public function rechazar(Request $request, AjusteInventario $ajuste)
    {
        if (!$ajuste->esPendiente()) {
            return redirect()->route('ajustes.show', $ajuste)
                ->with('error', 'Este ajuste ya fue procesado.');
        }

        $ajuste->update(['estado' => 'rechazado']);

        return redirect()->route('ajustes.show', $ajuste)
            ->with('success', 'Ajuste rechazado.');
    }

    public function buscarStockVariante(Request $request)
    {
        $varianteId = $request->get('variante_id');
        $almacenId  = $request->get('almacen_id');

        $stock = Stock::where('variante_producto_id', $varianteId)
                      ->where('almacen_id', $almacenId)
                      ->first();

        return response()->json([
            'cantidad_sistema' => $stock?->cantidad_disponible ?? 0,
        ]);
    }
}