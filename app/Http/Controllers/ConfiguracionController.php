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
            'itbis_porcentaje'           => 'required|numeric|min:0|max:100',
            'negocio_nombre'             => 'required|string|max:150',
            'negocio_rnc'                => 'nullable|string|max:20',
            'negocio_email'              => 'nullable|email|max:100',
            'factura_mensaje_pie'        => 'nullable|string|max:255',
            'devolucion_dias_limite'     => 'required|integer|min:1',
            'caja_monto_minimo_apertura' => 'required|numeric|min:0',
            'horario_apertura'           => 'required|date_format:H:i',
            'horario_cierre'             => 'required|date_format:H:i',
        ], [
            'itbis_porcentaje.required' => 'El porcentaje de ITBIS es obligatorio.',
            'itbis_porcentaje.numeric'  => 'El porcentaje debe ser un número.',
            'negocio_nombre.required'   => 'El nombre del negocio es obligatorio.',
            'negocio_email.email'       => 'Ingresa un correo válido.',
            'devolucion_dias_limite.required'     => 'El límite de días para devolución es obligatorio.',
            'caja_monto_minimo_apertura.required' => 'El monto mínimo de apertura de caja es obligatorio.',
            'horario_apertura.date_format' => 'La hora de apertura debe tener formato HH:MM.',
            'horario_cierre.date_format'   => 'La hora de cierre debe tener formato HH:MM.',
        ]);

        $campos = [
            'itbis_porcentaje',
            'negocio_nombre',
            'negocio_rnc',
            'negocio_email',
            'factura_mensaje_pie',
            'devolucion_dias_limite',
            'caja_monto_minimo_apertura',
            'horario_apertura',
            'horario_cierre',
        ];

        foreach ($campos as $clave) {
    Configuracion::set($clave, (string) $request->input($clave, ''));
}

        return redirect()->route('configuraciones.index')
            ->with('success', 'Configuración actualizada correctamente.');
    }
}