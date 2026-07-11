<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configs = Configuracion::all()->keyBy('clave');
        return view('configuraciones.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'itbis_porcentaje'    => 'required|numeric|min:0|max:100',
            'negocio_nombre'      => 'required|string|max:150',
            'negocio_rnc'         => 'nullable|string|max:20',
            'negocio_email'       => 'nullable|email|max:100',
            'factura_mensaje_pie' => 'nullable|string|max:255',
        ], [
            'itbis_porcentaje.required' => 'El porcentaje de ITBIS es obligatorio.',
            'itbis_porcentaje.numeric'  => 'El porcentaje debe ser un número.',
            'negocio_nombre.required'   => 'El nombre del negocio es obligatorio.',
            'negocio_email.email'       => 'Ingresa un correo válido.',
        ]);

        $campos = [
            'itbis_porcentaje',
            'negocio_nombre',
            'negocio_rnc',
            'negocio_email',
            'factura_mensaje_pie',
        ];

        foreach ($campos as $clave) {
            Configuracion::set($clave, $request->input($clave, ''));
        }

        return redirect()->route('configuraciones.index')
            ->with('success', 'Configuración actualizada correctamente.');
    }
}