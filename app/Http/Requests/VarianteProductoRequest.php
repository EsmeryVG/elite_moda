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
            'color' => 'required|string|max:255',
            'talla' => 'required|string|max:255',
            'material' => 'required|string|max:255',
            'precio_venta' => 'required|numeric|min:0',
        ];
    }
}
