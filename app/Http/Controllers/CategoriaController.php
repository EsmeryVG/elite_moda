<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaRequest;
use App\Models\Categoria;


    class CategoriaController extends Controller
    {
        /**
         * Display a listing of the resource.
         */
      public function index()
        {
             $categorias = Categoria::orderBy('estado', 'desc')
                            ->orderBy('nombre', 'asc')
                            ->get();

             return view('categorias.index', compact('categorias'));
        }

        /**
         * Show the form for creating a new resource.
         */
        public function create()
        {
            return view('categorias.create');
        }

        /**
         * Store a newly created resource in storage.
         */
    public function store(CategoriaRequest $request)
    {
        $data = $request->validated();
        $data['nombre']      = trim($data['nombre']);
        $data['descripcion'] = isset($data['descripcion']) ? trim($data['descripcion']) : null;
        $data['estado']      = true; // siempre activa al crear

        $categoria = Categoria::create($data);

        $categoria->codigo = 'CAT-' . str_pad($categoria->id, 3, '0', STR_PAD_LEFT);
        $categoria->save();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

        /**
         * Display the specified resource.
         */
        public function show(Categoria $categoria)
    {
        return view('categorias.show', compact('categoria'));
    }

        /**
         * Show the form for editing the specified resource.
         */
        public function edit(Categoria $categoria)
        {
            return view('categorias.edit', compact('categoria'));
        }

        /**
         * Update the specified resource in storage.
         */
    public function update(CategoriaRequest $request, Categoria $categoria)
    {
        $data = $request->validated();
        $data['nombre']      = trim($data['nombre']);
        $data['descripcion'] = isset($data['descripcion']) ? trim($data['descripcion']) : null;
        // estado viene del formulario de edición (toggle activo/inactivo)
        $data['estado']      = $request->boolean('estado');

        $categoria->update($data);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }


        /**
         * Remove the specified resource from storage.
         */
    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->exists()) {
            return redirect()->route('categorias.index')
                ->with('error', 'No puedes desactivar esta categoría porque tiene productos asociados.');
        }

        $categoria->update(['estado' => false]);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría desactivada correctamente.');
    }

        /**
         * En caso de que se quiera reactivar una categoría, este método la pondrá nuevamente como activa. 
         * Se asume que la ruta y el botón para esta acción solo estarán disponibles para categorías inactivas.
         */
    public function reactivar(Categoria $categoria)
{
    $categoria->update(['estado' => true]);

    return redirect()->route('categorias.index')
        ->with('success', 'Categoría reactivada correctamente.');
}
    }
