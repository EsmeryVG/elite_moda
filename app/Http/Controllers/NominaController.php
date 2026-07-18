<?php

namespace App\Http\Controllers;

use App\Models\Comision;
use App\Models\Configuracion;
use App\Models\DetalleNomina;
use App\Models\Empleado;
use App\Models\Nomina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NominaController extends Controller
{
    public function index()
    {
        $nominas = Nomina::orderByDesc('periodo_inicio')->paginate(15);

        [$periodoInicio, $periodoFin] = $this->calcularPeriodoActual();

        $yaExiste = Nomina::where('periodo_inicio', $periodoInicio)
            ->where('periodo_fin', $periodoFin)
            ->exists();

        return view('nomina.index', compact('nominas', 'periodoInicio', 'periodoFin', 'yaExiste'));
    }

    private function calcularPeriodoActual(): array
    {
        $ciclo = Configuracion::get('nomina_ciclo', 'mensual');
        $hoy = now();

        if ($ciclo === 'quincenal') {
            if ($hoy->day <= 15) {
                return [$hoy->copy()->startOfMonth(), $hoy->copy()->day(15)];
            }
            return [$hoy->copy()->day(16), $hoy->copy()->endOfMonth()];
        }

        return [$hoy->copy()->startOfMonth(), $hoy->copy()->endOfMonth()];
    }

    public function generar(Request $request)
    {
        [$periodoInicio, $periodoFin] = $this->calcularPeriodoActual();

        $yaExiste = Nomina::where('periodo_inicio', $periodoInicio)
            ->where('periodo_fin', $periodoFin)
            ->exists();

        if ($yaExiste) {
            return back()->with('error', 'Ya existe una nómina generada para este período.');
        }

        try {
            $nomina = DB::transaction(function () use ($periodoInicio, $periodoFin) {
                $nomina = Nomina::create([
                    'periodo_inicio' => $periodoInicio,
                    'periodo_fin' => $periodoFin,
                    'fecha_pago' => now(),
                    'usuario_id' => Auth::id(),
                    'estado' => 'pendiente',
                ]);

                $totalGeneral = 0;
                $empleados = Empleado::where('estado', true)->get();

                foreach ($empleados as $empleado) {
                    $comisiones = Comision::where('empleado_id', $empleado->id)
                        ->where('estado', 'pendiente')
                        ->whereBetween('fecha', [$periodoInicio, $periodoFin])
                        ->get();

                    $totalComisiones = $comisiones->sum('monto_comision');
                    $totalPagar = $empleado->salario_base + $totalComisiones;

                    $detalle = DetalleNomina::create([
                        'nomina_id' => $nomina->id,
                        'empleado_id' => $empleado->id,
                        'salario_base' => $empleado->salario_base,
                        'total_comisiones' => $totalComisiones,
                        'total_pagar' => $totalPagar,
                    ]);

                    Comision::whereIn('id', $comisiones->pluck('id'))
                        ->update(['detalle_nomina_id' => $detalle->id]);

                    $totalGeneral += $totalPagar;
                }

                $nomina->update(['total_general' => $totalGeneral]);

                return $nomina;
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return redirect()->route('nomina.index')
                ->with('error', 'Ya existe una nómina generada para este período (se detectó al guardar).');
        }

        return redirect()->route('nomina.show', $nomina)
            ->with('success', 'Nómina generada correctamente.');
    }

    public function show(Nomina $nomina)
    {
        $nomina->load('detalles.empleado', 'usuario');

        return view('nomina.show', compact('nomina'));
    }

    public function detalleEmpleado(DetalleNomina $detalleNomina)
    {
        $detalleNomina->load('empleado', 'nomina', 'comisiones.venta.cliente');

        return view('nomina.detalle_empleado', compact('detalleNomina'));
    }

    public function marcarPagada(Nomina $nomina)
    {
        abort_unless(Auth::user()->tienePermiso('nomina.gestionar'), 403);
        abort_unless($nomina->estado === 'pendiente', 422, 'Esta nómina ya fue marcada como pagada.');

        DB::transaction(function () use ($nomina) {
            $nomina->update(['estado' => 'pagada']);

            $detalleIds = $nomina->detalles()->pluck('id');
            Comision::whereIn('detalle_nomina_id', $detalleIds)->update(['estado' => 'pagada']);
        });

        return back()->with('success', 'Nómina marcada como pagada.');
    }
}