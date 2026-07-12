<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Configuracion;
use App\Models\ComprobanteFiscal;
use App\Models\Devolucion;
use App\Models\DetalleDevolucion;
use App\Models\Empleado;
use App\Models\MovimientoInventario;
use App\Models\NotaCredito;
use App\Models\Stock;
use App\Models\Sucursal;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DevolucionController extends Controller
{
    public function index(Request $request)
    {
        $devoluciones = $this->construirQuery($request)->paginate(15);

        return view('devoluciones.index', compact('devoluciones'));
    }

    public function tabla(Request $request)
    {
        $devoluciones = $this->construirQuery($request)->paginate(15);

        return view('devoluciones._tabla', compact('devoluciones'));
    }

    private function construirQuery(Request $request)
    {
        $query = Devolucion::with(['venta', 'cliente', 'usuario', 'empleado']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->where(function ($q) use ($b) {
                $q->where('codigo', 'like', "%{$b}%")
                  ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$b}%"));
            });
        }

        return $query->orderByDesc('fecha');
    }

    public function create(Venta $venta)
    {
        abort_unless($venta->estado === 'completada', 404);

        $diasLimite = (int) Configuracion::get('devolucion_dias_limite', 30);
        $diasTranscurridos = (int) $venta->fecha->diffInDays(now());
        $fueraDeTiempo = $diasTranscurridos > $diasLimite;

        $lineas = $venta->detalles->map(function ($detalle) {
            $yaDevuelto = DetalleDevolucion::where('detalle_venta_id', $detalle->id)->sum('cantidad');

            return [
                'detalle_venta_id' => $detalle->id,
                'variante' => $detalle->variante,
                'cantidad_original' => $detalle->cantidad,
                'cantidad_devuelta' => $yaDevuelto,
                'cantidad_disponible' => $detalle->cantidad - $yaDevuelto,
                'precio_unitario' => $detalle->precio_unitario, // incluye ITBIS
                'permite_devolucion' => $detalle->variante->producto->permite_devolucion,
            ];
        })->filter(fn ($l) => $l['cantidad_disponible'] > 0 && $l['permite_devolucion']);

        return view('devoluciones.create', compact('venta', 'lineas', 'fueraDeTiempo', 'diasTranscurridos', 'diasLimite'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'empleado_id' => 'required|exists:empleados,id',
            'admin_password' => 'nullable|string',
            'lineas' => 'required|array|min:1',
            'lineas.*.detalle_venta_id' => 'required|exists:detalle_ventas,id',
            'lineas.*.cantidad' => 'required|integer|min:1',
            'lineas.*.motivo' => 'required|in:talla_incorrecta,no_satisfaccion,defecto_fabrica,producto_danado,error_facturacion,otro',
        ]);

        $venta = Venta::with('detalles.variante.producto', 'cliente')->findOrFail($validated['venta_id']);

        $diasLimite = (int) Configuracion::get('devolucion_dias_limite', 30);
        $diasTranscurridos = (int) $venta->fecha->diffInDays(now());
        $fueraDeTiempo = $diasTranscurridos > $diasLimite;

        // Condición 1: autorización de admin si supera el límite de días
        $autorizadoPor = null;
        if ($fueraDeTiempo) {
            if (empty($validated['admin_password'])) {
                return back()->withErrors([
                    'admin_password' => "Esta devolución supera los {$diasLimite} días y requiere autorización de un administrador.",
                ])->withInput();
            }

            $admin = Auth::user()->esAdministrador() ? Auth::user() : null;

            if (! $admin) {
                $admin = \App\Models\User::whereHas('rol', fn ($q) => $q->where('nombre', 'Administrador'))
                    ->activos()
                    ->get()
                    ->first(fn ($u) => Hash::check($validated['admin_password'], $u->password));
            } elseif (! Hash::check($validated['admin_password'], $admin->password)) {
                $admin = null;
            }

            if (! $admin) {
                return back()->withErrors(['admin_password' => 'Contraseña de administrador incorrecta.'])->withInput();
            }

            $autorizadoPor = $admin->id;
        }

        // Validar disponibilidad y elegibilidad por línea
        foreach ($validated['lineas'] as $linea) {
            $detalleVenta = $venta->detalles->firstWhere('id', $linea['detalle_venta_id']);

            if (! $detalleVenta || ! $detalleVenta->variante->producto->permite_devolucion) {
                return back()->withErrors(['lineas' => 'Uno de los productos no admite devolución.'])->withInput();
            }

            $yaDevuelto = DetalleDevolucion::where('detalle_venta_id', $detalleVenta->id)->sum('cantidad');
            $disponible = $detalleVenta->cantidad - $yaDevuelto;

            if ($linea['cantidad'] > $disponible) {
                return back()->withErrors([
                    'lineas' => "La cantidad a devolver de {$detalleVenta->variante->producto->nombre} excede lo disponible ({$disponible}).",
                ])->withInput();
            }
        }

        $empleado = Empleado::where('id', $validated['empleado_id'])->where('estado', true)->first();
        abort_unless($empleado, 422, 'El empleado seleccionado no está activo.');
        $empleadoId = $empleado->id;

        $itbisPorcentaje = (float) Configuracion::get('itbis_porcentaje', 18);

        $devolucion = DB::transaction(function () use ($validated, $venta, $fueraDeTiempo, $diasTranscurridos, $autorizadoPor, $empleadoId, $itbisPorcentaje) {

            $devolucion = Devolucion::create([
                'venta_id' => $venta->id,
                'cliente_id' => $venta->cliente_id,
                'usuario_id' => Auth::id(),
                'empleado_id' => $empleadoId,
                'codigo' => 'DEV-' . str_pad((Devolucion::max('id') + 1), 6, '0', STR_PAD_LEFT),
                'fecha' => now(),
                'motivo' => $validated['lineas'][0]['motivo'],
                'estado' => 'pendiente',
                'requiere_autorizacion' => $fueraDeTiempo,
                'autorizado_por' => $autorizadoPor,
                'dias_desde_venta' => $diasTranscurridos,
                'incluye_itbis' => ! $fueraDeTiempo,
                'total' => 0,
            ]);

            $totalNC = 0;

            foreach ($validated['lineas'] as $linea) {
                $detalleVenta = $venta->detalles->firstWhere('id', $linea['detalle_venta_id']);

                $precioUnitario = $detalleVenta->precio_unitario; // incluye ITBIS
                $subtotalLinea = round($precioUnitario * $linea['cantidad'], 2);

                // precio_unitario incluye ITBIS -> se extrae, no se suma
                $precioBaseUnitario = $precioUnitario / (1 + $itbisPorcentaje / 100);
                $itbisPorUnidad = $precioUnitario - $precioBaseUnitario;
                $itbisLinea = round($itbisPorUnidad * $linea['cantidad'], 2);

                DetalleDevolucion::create([
                    'devolucion_id' => $devolucion->id,
                    'variante_producto_id' => $detalleVenta->variante_producto_id,
                    'detalle_venta_id' => $detalleVenta->id,
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotalLinea,
                    'motivo' => $linea['motivo'],
                    'condicion_inspeccion' => 'pendiente',
                    'itbis_linea' => $itbisLinea,
                ]);

                $totalNC += $devolucion->incluye_itbis ? $subtotalLinea : ($subtotalLinea - $itbisLinea);
            }

            $devolucion->update(['total' => round($totalNC, 2)]);

            $comprobanteB04 = ComprobanteFiscal::activos()
                ->where('tipo_comprobante', 'B04')
                ->firstOrFail();

            $ncf = $comprobanteB04->siguienteNumero();
            $comprobanteB04->increment('numero_actual');

            NotaCredito::create([
                'devolucion_id' => $devolucion->id,
                'cliente_id' => $venta->cliente_id,
                'codigo' => NotaCredito::generarCodigo(),
                'ncf' => $ncf,
                'monto_original' => $devolucion->total,
                'monto_disponible' => $devolucion->total,
                'fecha' => now(),
                'fecha_vencimiento' => now()->addDays(90),
                'estado' => 'activa',
            ]);

            return $devolucion;
        });

        return redirect()->route('devoluciones.show', $devolucion)
            ->with('success', 'Devolución registrada. Nota de crédito ' . $devolucion->notaCredito->codigo . ' generada. Pendiente de inspección.');
    }

    public function show(Devolucion $devolucion)
    {
        $devolucion->load('venta', 'cliente', 'usuario', 'empleado', 'detalles.variante.producto', 'notaCredito');

        return view('devoluciones.show', compact('devolucion'));
    }

    public function inspeccionar(Request $request, DetalleDevolucion $detalleDevolucion)
    {
        $request->validate([
            'condicion' => 'required|in:conforme,no_conforme',
        ]);

        abort_unless($detalleDevolucion->condicion_inspeccion === 'pendiente', 422, 'Esta línea ya fue inspeccionada.');

        DB::transaction(function () use ($detalleDevolucion, $request) {
            $detalleDevolucion->update(['condicion_inspeccion' => $request->condicion]);

            $variante = $detalleDevolucion->variante;
            $venta = $detalleDevolucion->devolucion->venta;
            $almacenId = $venta->almacen_id ?? $this->obtenerAlmacenVenta();

            if ($request->condicion === 'conforme') {
                Stock::incrementar($variante->id, $almacenId, $detalleDevolucion->cantidad);

                MovimientoInventario::registrar(
                    $variante->id,
                    $almacenId,
                    'entrada_devolucion',
                    $detalleDevolucion->cantidad,
                    'devolucion',
                    $detalleDevolucion->devolucion_id,
                    'Devolución conforme - ' . $detalleDevolucion->motivo,
                    Auth::id()
                );
            } else {
                MovimientoInventario::registrar(
                    $variante->id,
                    $almacenId,
                    'ajuste_negativo',
                    $detalleDevolucion->cantidad,
                    'devolucion',
                    $detalleDevolucion->devolucion_id,
                    'Merma por devolución no conforme - ' . $detalleDevolucion->motivo,
                    Auth::id()
                );
            }

            $devolucion = $detalleDevolucion->devolucion()->with('detalles')->first();
            if ($devolucion->inspeccion_completa) {
                $devolucion->update(['estado' => 'aprobada']);
            }
        });

        return back()->with('success', 'Línea inspeccionada correctamente.');
    }

    /**
     * Duplicado deliberadamente de VentaController::obtenerAlmacenVenta().
     * Es fallback (venta.almacen_id es nullable) — igual criterio que en Ventas.
     */
    private function obtenerAlmacenVenta(): ?int
    {
        $sucursalPrincipal = Sucursal::where('es_principal', true)->first();

        if ($sucursalPrincipal) {
            $almacen = Almacen::where('estado', true)
                ->where('sucursal_id', $sucursalPrincipal->id)
                ->where('tipo', 'secundario')
                ->first();

            if ($almacen) return $almacen->id;
        }

        $almacenSecundario = Almacen::where('estado', true)
            ->where('tipo', 'secundario')
            ->first();

        if ($almacenSecundario) return $almacenSecundario->id;

        return Almacen::where('estado', true)->first()?->id;
    }
}