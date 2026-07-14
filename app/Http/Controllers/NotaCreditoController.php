<?php

namespace App\Http\Controllers;

use App\Models\NotaCredito;
use Illuminate\Http\Request;

class NotaCreditoController extends Controller
{
    public function index(Request $request)
    {
        $notas = $this->construirQuery($request)->paginate(15);

        return view('notas_credito.index', compact('notas'));
    }

    public function tabla(Request $request)
    {
        $notas = $this->construirQuery($request)->paginate(15);

        return view('notas_credito._tabla', compact('notas'));
    }

    private function construirQuery(Request $request)
    {
        $query = NotaCredito::with('cliente', 'devolucion.venta');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->where(function ($q) use ($b) {
                $q->where('codigo', 'like', "%{$b}%")
                  ->orWhere('ncf', 'like', "%{$b}%")
                  ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$b}%"));
            });
        }

        return $query->orderByDesc('fecha');
    }

    public function show(NotaCredito $notaCredito)
    {
        $notaCredito->load('cliente', 'devolucion.venta', 'devolucion.empleado', 'devolucion.detalles.variante.producto');

        $pagosConNC = \App\Models\Pago::whereHas('tipoPago', fn ($q) => $q->where('nombre', 'Nota de Crédito'))
            ->where('referencia', $notaCredito->codigo)
            ->with('venta')
            ->orderBy('fecha')
            ->get();

        return view('notas_credito.show', compact('notaCredito', 'pagosConNC'));
    }
}