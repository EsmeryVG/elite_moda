<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $proveedorId = $this->route('proveedor')?->id;

        return [
            'nombre'          => [
                'required', 'string', 'max:150',
                Rule::unique('proveedores', 'nombre')->ignore($proveedorId),
            ],
            'rnc'             => [
                'nullable', 'string',
                'regex:/^[0-9]{9,11}$/',
                Rule::unique('proveedores', 'rnc')->ignore($proveedorId),
            ],
            'telefono'        => [
                'nullable', 'string',
                'regex:/^[0-9]{10}$/',
            ],
            'email'           => [
                'nullable', 'email', 'max:150',
                Rule::unique('proveedores', 'email')->ignore($proveedorId),
            ],
            'direccion'       => 'nullable|string|max:255',
            'contacto_nombre' => 'nullable|string|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.unique'   => 'Ya existe un proveedor con ese nombre.',
            'rnc.unique'      => 'Este RNC ya está registrado.',
            'rnc.regex'       => 'El RNC debe tener entre 9 y 11 dígitos numéricos.',
            'telefono.regex'  => 'El teléfono debe tener exactamente 10 dígitos numéricos.',
            'email.email'     => 'El email no tiene un formato válido.',
            'email.unique'    => 'Este email ya está registrado.',
        ];
    }
}