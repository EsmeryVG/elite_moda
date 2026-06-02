<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SucursalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sucursalId = $this->route('sucursal')?->id;

        return [
            'nombre'    => [
                'required', 'string', 'max:100',
                Rule::unique('sucursales', 'nombre')->ignore($sucursalId),
            ],
            'direccion' => 'nullable|string|max:255',
            'telefono' => ['nullable', 'string', 'regex:/^[0-9]{10}$/'],
            'es_principal' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la sucursal es obligatorio.',
            'nombre.unique'   => 'Ya existe una sucursal con ese nombre.',
            'telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos numéricos.',
        ];
    }
}