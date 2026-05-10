<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VarianteProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id' => 'required|exists:productos,id',
            'descripcion' => 'nullable|string',
            'atributo_valor_ids' => 'required|array|min:1',
            'atributo_valor_ids.*' => 'required|exists:atributo_valores,id',
            'precio_venta' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'atributo_valor_ids.required' => 'Debe seleccionar al menos un atributo con su valor.',
            'atributo_valor_ids.array' => 'Los valores de atributos deben enviarse correctamente.',
            'atributo_valor_ids.min' => 'Debe seleccionar al menos un atributo con su valor.',
            'atributo_valor_ids.*.exists' => 'Uno de los valores seleccionados no existe.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric' => 'El precio de venta debe ser numérico.',
        ];
    }
}