<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;

class MovimientoInventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = MovimientoInventario::with([
            'variante.producto',
            'variante.valores.atributo',
            'almacen.sucursal',
            'usuario',
        ])->orderBy('fecha', 'desc');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('almacen')) {
            $query->where('almacen_id', $request->almacen);
        }

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->whereHas('variante.producto', fn ($sub) => $sub->where('nombre', 'like', "%{$b}%"))
                  ->orWhereHas('variante', fn ($sub) => $sub->where('codigo', 'like', "%{$b}%")
                      ->orWhere('codigo_barras', 'like', "%{$b}%"))
                  ->orWhere('motivo', 'like', "%{$b}%");
            });
        }

        $movimientos = $query->paginate(15)->withQueryString();
        $almacenes   = Almacen::where('estado', true)->orderBy('nombre')->get();

        if ($request->ajax()) {
            return view('movimientos._tabla', compact('movimientos'))->render();
        }

        return view('movimientos.index', compact('movimientos', 'almacenes'));
    }
}