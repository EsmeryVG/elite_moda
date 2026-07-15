<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::with([
            'variante.producto.categoria',
            'variante.valores.atributo',
            'almacen.sucursal',
        ]);

        if ($request->filled('almacen')) {
            $query->where('almacen_id', $request->almacen);
        }

        if ($request->filled('buscar')) {
            $query->whereHas('variante.producto', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('codigo', 'like', '%' . $request->buscar . '%');
            })->orWhereHas('variante', function ($q) use ($request) {
                $q->where('codigo', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('nivel')) {
        $query->when($request->nivel === 'agotado', fn($q) =>
            $q->where('cantidad_disponible', '<=', 0)
        )->when($request->nivel === 'critico', fn($q) =>
            $q->whereColumn('cantidad_disponible', '<=', 'stock_minimo')
            ->where('cantidad_disponible', '>', 0)
        )->when($request->nivel === 'ok', fn($q) =>
            $q->whereColumn('cantidad_disponible', '>', 'stock_minimo')
        );
    }

        $stocks   = $query->orderBy('cantidad_disponible', 'asc')->paginate(15)->withQueryString();
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();

        if ($request->ajax()) {
            return view('stock._tabla', compact('stocks'))->render();
        }

        return view('stock.index', compact('stocks', 'almacenes'));
    }
}