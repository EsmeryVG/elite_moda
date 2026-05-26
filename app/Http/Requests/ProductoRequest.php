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
        $productoId = $this->route('producto')?->id;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('productos')
                    ->where('marca', $this->input('marca') === '__otra__'
                        ? trim(ucwords(strtolower($this->input('nueva_marca') ?? '')))
                        : trim(ucwords(strtolower($this->input('marca') ?? ''))))
                    ->where('categoria_id', $this->input('categoria_id'))
                    ->ignore($productoId),
            ],
            'descripcion'  => 'nullable|string',
            'marca'        => 'nullable|string|max:255',
            'nueva_marca'  => 'nullable|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'precio_simple' => 'required_without:variantes|nullable|numeric|min:0',
            'variantes'                        => 'nullable|array',
            'variantes.*.precio_venta'         => 'required_with:variantes|numeric|min:0',
            'variantes.*.atributo_valor_ids'   => 'required_with:variantes|array|min:1',
            'variantes.*.atributo_valor_ids.*' => 'exists:atributo_valores,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'          => 'El nombre del producto es obligatorio.',
            'nombre.unique'            => 'Ya existe un producto con ese nombre, marca y categoría.',
            'categoria_id.required'    => 'Debes seleccionar una categoría.',
            'categoria_id.exists'      => 'La categoría seleccionada no existe.',
            'precio_simple.required_without' => 'El precio es obligatorio para productos simples.',
            'variantes.*.precio_venta.required_with' => 'Cada variante debe tener un precio.',
        ];
    }
}