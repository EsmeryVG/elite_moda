<?php

namespace App\Http\Controllers;

use App\Models\ComprobanteFiscal;
use Illuminate\Http\Request;

class ComprobanteFiscalController extends Controller
{
    public function index()
    {
        $comprobantes = ComprobanteFiscal::orderBy('estado', 'desc')
                                         ->orderBy('tipo_comprobante')
                                         ->get();

        return view('comprobantes.index', compact('comprobantes'));
    }

    public function create()
    {
        return view('comprobantes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_comprobante'   => 'required|string|max:100',
            'prefijo_ncf'        => 'required|string|max:5|unique:comprobantes_fiscales,prefijo_ncf',
            'rango_inicio'       => 'required|integer|min:1',
            'rango_fin'          => 'required|integer|gt:rango_inicio',
            'fecha_vencimiento'  => 'required|date|after:today',
        ], [
            'tipo_comprobante.required' => 'El tipo de comprobante es obligatorio.',
            'prefijo_ncf.required'      => 'El prefijo NCF es obligatorio.',
            'prefijo_ncf.unique'        => 'Ya existe un comprobante con ese prefijo.',
            'rango_fin.gt'               => 'El rango final debe ser mayor al rango inicial.',
            'fecha_vencimiento.after'    => 'La fecha de vencimiento debe ser futura.',
        ]);

        ComprobanteFiscal::create([
            'tipo_comprobante'  => $request->tipo_comprobante,
            'prefijo_ncf'       => strtoupper($request->prefijo_ncf),
            'rango_inicio'      => $request->rango_inicio,
            'rango_fin'         => $request->rango_fin,
            'numero_actual'     => $request->rango_inicio,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'estado'            => true,
        ]);

        return redirect()->route('comprobantes.index')
            ->with('success', 'Comprobante fiscal registrado correctamente.');
    }

    public function show(ComprobanteFiscal $comprobante)
    {
        $comprobante->load('ventas');
        return view('comprobantes.show', compact('comprobante'));
    }

    public function edit(ComprobanteFiscal $comprobante)
    {
        return view('comprobantes.edit', compact('comprobante'));
    }

    public function update(Request $request, ComprobanteFiscal $comprobante)
    {
        $request->validate([
            'tipo_comprobante'   => 'required|string|max:100',
            'prefijo_ncf'        => 'required|string|max:5|unique:comprobantes_fiscales,prefijo_ncf,' . $comprobante->id,
            'rango_inicio'       => 'required|integer|min:1',
            'rango_fin'          => 'required|integer|gt:rango_inicio',
            'fecha_vencimiento'  => 'required|date',
        ], [
            'tipo_comprobante.required' => 'El tipo de comprobante es obligatorio.',
            'prefijo_ncf.required'      => 'El prefijo NCF es obligatorio.',
            'prefijo_ncf.unique'        => 'Ya existe un comprobante con ese prefijo.',
            'rango_fin.gt'               => 'El rango final debe ser mayor al rango inicial.',
        ]);

        // El rango no puede reducirse por debajo de lo ya consumido
        if ($request->rango_fin < $comprobante->numero_actual) {
            return back()->withErrors([
                'rango_fin' => 'El rango final no puede ser menor al número ya consumido (' . $comprobante->numero_actual . ').',
            ])->withInput();
        }

        if ($request->rango_inicio > $comprobante->numero_actual && $comprobante->numero_actual > $comprobante->rango_inicio) {
            return back()->withErrors([
                'rango_inicio' => 'No puedes subir el rango de inicio por encima del número ya consumido (' . $comprobante->numero_actual . ').',
            ])->withInput();
        }

        $comprobante->update([
            'tipo_comprobante'  => $request->tipo_comprobante,
            'prefijo_ncf'       => strtoupper($request->prefijo_ncf),
            'rango_inicio'      => $request->rango_inicio,
            'rango_fin'         => $request->rango_fin,
            'fecha_vencimiento' => $request->fecha_vencimiento,
        ]);

        return redirect()->route('comprobantes.index')
            ->with('success', 'Comprobante fiscal actualizado correctamente.');
    }

    public function desactivar(ComprobanteFiscal $comprobante)
    {
        $comprobante->update(['estado' => false]);

        return redirect()->route('comprobantes.index')
            ->with('success', 'Comprobante fiscal desactivado correctamente.');
    }

    public function reactivar(ComprobanteFiscal $comprobante)
    {
        $comprobante->update(['estado' => true]);

        return redirect()->route('comprobantes.index')
            ->with('success', 'Comprobante fiscal reactivado correctamente.');
    }
}