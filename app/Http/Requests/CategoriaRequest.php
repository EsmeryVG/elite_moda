<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CategoriaRequest extends FormRequest
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
        // En edición ignoramos el registro actual para el unique
        $categoriaId = $this->route('categoria')?->id;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('categorias', 'nombre')
                    ->ignore($categoriaId),
            ],
            'descripcion' => 'nullable|string',
            'estado'      => 'nullable|boolean',
        ];
    }

        public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique'   => 'Ya existe una categoría con ese nombre.',
            'nombre.max'      => 'El nombre no puede superar los 255 caracteres.',
        ];
    }
}
