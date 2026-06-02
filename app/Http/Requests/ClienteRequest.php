<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clienteId = $this->route('cliente')?->id;

        return [
            'nombre'           => 'required|string|max:150',
            'apellido'         => 'nullable|string|max:150',
            'cedula'           => [
                'nullable', 'string',
                'regex:/^[0-9]{11}$/',
                Rule::unique('clientes', 'cedula')->ignore($clienteId),
            ],
            'rnc'              => [
                'nullable', 'string',
                'regex:/^[0-9]{9}$|^[0-9]{11}$/',
                Rule::unique('clientes', 'rnc')->ignore($clienteId),
            ],
            'telefono'         => [
                'nullable', 'string',
                'regex:/^[0-9]{10}$/',
            ],
            'email'            => [
                'nullable', 'email', 'max:150',
                Rule::unique('clientes', 'email')->ignore($clienteId),
            ],
            'direccion'        => 'nullable|string|max:255',
            'grupo_cliente_id' => 'nullable|exists:grupo_clientes,id',
            'limite_credito'   => 'nullable|numeric|min:0',
            'credito_activo'   => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'    => 'El nombre del cliente es obligatorio.',
            'cedula.unique'      => 'Esta cédula ya está registrada.',
            'cedula.regex'       => 'La cédula debe tener exactamente 11 dígitos.',
            'rnc.unique'         => 'Este RNC ya está registrado.',
            'rnc.regex'          => 'El RNC debe tener 9 dígitos (empresa) u 11 dígitos (persona física).',
            'telefono.regex'     => 'El teléfono debe tener exactamente 10 dígitos numéricos.',
            'email.email'        => 'El email no tiene un formato válido.',
            'email.unique'       => 'Este email ya está registrado.',
            'limite_credito.min' => 'El límite de crédito no puede ser negativo.',
        ];
    }
}