<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Info general
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'marca'        => 'nullable|string|max:255',
            'nueva_marca'  => 'nullable|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',

            // Producto simple
            'precio_simple' => 'required_without:variantes|nullable|numeric|min:0',
            'costo_simple'  => 'nullable|numeric|min:0',

            // Variantes (cuando hay grilla)
            'variantes'                          => 'nullable|array',
            'variantes.*.precio_venta'           => 'required_with:variantes|numeric|min:0',
            'variantes.*.costo'                  => 'nullable|numeric|min:0',
            'variantes.*.codigo_barras'          => 'nullable|string|max:100',
            'variantes.*.descuento_maximo'       => 'nullable|numeric|min:0|max:100',
            'variantes.*.atributo_valor_ids'     => 'required_with:variantes|array|min:1',
            'variantes.*.atributo_valor_ids.*'   => 'exists:atributo_valores,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'          => 'El nombre del producto es obligatorio.',
            'categoria_id.required'    => 'Debes seleccionar una categoría.',
            'categoria_id.exists'      => 'La categoría seleccionada no existe.',
            'precio_simple.required_without' => 'El precio es obligatorio para productos simples.',
            'variantes.*.precio_venta.required_with' => 'Cada variante debe tener un precio de venta.',
        ];
    }
}