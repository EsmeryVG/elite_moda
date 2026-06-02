<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlmacenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $almacenId = $this->route('almacen')?->id;

        return [
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'nombre'      => [
                'required', 'string', 'max:100',
                Rule::unique('almacenes', 'nombre')
                    ->where('sucursal_id', $this->input('sucursal_id'))
                    ->ignore($almacenId),
            ],
            'tipo'        => 'required|in:principal,secundario',
            'direccion'   => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del almacén es obligatorio.',
            'nombre.unique'   => 'Ya existe un almacén con ese nombre en esta sucursal.',
            'tipo.required'   => 'El tipo de almacén es obligatorio.',
            'tipo.in'         => 'El tipo debe ser principal o secundario.',
        ];
    }
}