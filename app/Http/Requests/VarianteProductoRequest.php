<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VarianteProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
   public function rules(): array
    {
        return [
            'producto_id' => 'required|exists:productos,id',
            'descripcion' => 'nullable|string',

            'color' => 'nullable|string|max:100',
            'nuevo_color' => 'nullable|string|max:100',

            'talla' => 'nullable|string|max:50',
            'nueva_talla' => 'nullable|string|max:50',

            'material' => 'nullable|string|max:100',
            'nuevo_material' => 'nullable|string|max:100',

            'precio_venta' => 'required|numeric|min:0',
        ];
    }
}
