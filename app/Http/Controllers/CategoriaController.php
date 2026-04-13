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
         $categorias = Categoria::orderBy('id', 'desc')->get();
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
    Categoria::create($request->validated());

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
    $categoria->update($request->validated());

    return redirect()->route('categorias.index')
        ->with('success', 'Categoría actualizada correctamente.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
          $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
