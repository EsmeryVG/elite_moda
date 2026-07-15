<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Configuracion;
use App\Models\SesionCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SesionCajaController extends Controller
{
    public function index(Request $request)
    {
        $sesiones = $this->construirQuery($request)->paginate(15);

        return view('sesiones_caja.index', compact('sesiones'));
    }

    public function tabla(Request $request)
    {
        $sesiones = $this->construirQuery($request)->paginate(15);

        return view('sesiones_caja._tabla', compact('sesiones'));
    }

    private function construirQuery(Request $request)
    {
        $query = SesionCaja::with(['caja', 'usuarioApertura', 'usuarioCierre']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('caja_id')) {
            $query->where('caja_id', $request->caja_id);
        }

        return $query->orderByDesc('fecha_apertura');
    }

    public function formularioAbrir()
{
    abort_unless(Auth::user()->esAdministrador(), 403, 'Solo un administrador puede abrir una sesión de caja.');

    $cajasDisponibles = Caja::disponibles()->orderBy('nombre')->get();
    $montoMinimo = (float) Configuracion::get('caja_monto_minimo_apertura', 500);

    return view('sesiones_caja.abrir', compact('cajasDisponibles', 'montoMinimo'));
}

public function abrir(Request $request)
{
    abort_unless(Auth::user()->esAdministrador(), 403, 'Solo un administrador puede abrir una sesión de caja.');

    $montoMinimo = (float) Configuracion::get('caja_monto_minimo_apertura', 500);

    $validated = $request->validate([
        'caja_id' => 'required|exists:cajas,id',
        'monto_apertura' => "required|numeric|min:{$montoMinimo}",
    ], [
        'monto_apertura.min' => "El fondo de caja debe ser al menos RD$ {$montoMinimo}.",
    ]);

    $caja = Caja::activas()->findOrFail($validated['caja_id']);

    $sesionActivaEnCaja = SesionCaja::where('caja_id', $caja->id)->abiertas()->exists();
    if ($sesionActivaEnCaja) {
        return back()->withErrors(['caja_id' => 'Esta caja ya tiene una sesión abierta.'])->withInput();
    }

    DB::transaction(function () use ($caja, $validated) {
        $caja->asignarAlmacenSiFalta();

        SesionCaja::create([
            'caja_id' => $caja->id,
            'monto_apertura' => $validated['monto_apertura'],
            'fecha_apertura' => now(),
            'usuario_apertura_id' => Auth::id(),
            'estado' => 'abierta',
        ]);
    });

    return redirect()->route('ventas.create')->with('success', 'Sesión de caja abierta correctamente.');
}

    public function show(SesionCaja $sesionCaja)
    {
        $sesionCaja->load('caja', 'usuarioApertura', 'usuarioCierre', 'ventas.cliente');

        return view('sesiones_caja.show', compact('sesionCaja'));
    }

    public function formularioCerrar(SesionCaja $sesionCaja)
{
    abort_unless($sesionCaja->estado === 'abierta', 404);

    $montoEsperado = $sesionCaja->calcularMontoEsperado();
    $totalVentas = $sesionCaja->ventas()->count();
    $totalVentasMonto = $sesionCaja->ventas()->sum('total');

    return view('sesiones_caja.cerrar', compact('sesionCaja', 'montoEsperado', 'totalVentas', 'totalVentasMonto'));
}

public function cerrar(Request $request, SesionCaja $sesionCaja)
{
    abort_unless($sesionCaja->estado === 'abierta', 404);

    $validated = $request->validate([
        'monto_cierre_real' => 'required|numeric|min:0',
        'observacion_diferencia' => 'nullable|string',
    ]);

    $montoEsperado = $sesionCaja->calcularMontoEsperado();
    $diferencia = round($validated['monto_cierre_real'] - $montoEsperado, 2);

    if ($diferencia != 0 && empty($validated['observacion_diferencia'])) {
        return back()->withErrors([
            'observacion_diferencia' => 'Debes explicar la diferencia encontrada al cierre (RD$ ' . number_format($diferencia, 2) . ').',
        ])->withInput();
    }

    $sesionCaja->update([
        'monto_cierre_esperado' => $montoEsperado,
        'monto_cierre_real' => $validated['monto_cierre_real'],
        'diferencia' => $diferencia,
        'observacion_diferencia' => $validated['observacion_diferencia'] ?? null,
        'fecha_cierre' => now(),
        'usuario_cierre_id' => Auth::id(),
        'estado' => 'cerrada',
    ]);

    return redirect()->route('sesiones_caja.show', $sesionCaja)
        ->with('success', 'Sesión de caja cerrada correctamente.');
}

public function marcarRevisada(SesionCaja $sesionCaja)
{
    abort_unless(Auth::user()->esAdministrador(), 403);
    abort_unless($sesionCaja->diferencia != 0, 422, 'Esta sesión no tiene diferencia pendiente de revisión.');

    $sesionCaja->update([
        'revisada_por' => Auth::id(),
        'revisada_en' => now(),
    ]);

    return back()->with('success', 'Sesión marcada como revisada.');
}

public function pendientesRevision()
{
    $sesiones = SesionCaja::pendientesRevision()
        ->with('caja', 'usuarioCierre')
        ->orderByDesc('fecha_cierre')
        ->paginate(15);

    return view('sesiones_caja.pendientes_revision', compact('sesiones'));
}
}