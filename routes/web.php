<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VarianteProductoController;

Route::get('/', function () {
    return redirect()->route('productos.index');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::resource('categorias', CategoriaController::class);
Route::resource('productos', ProductoController::class);
Route::get('productos/{producto}/variantes/create', [VarianteProductoController::class, 'create'])->name('variantes.create');
Route::get('/variantes', [App\Http\Controllers\VarianteProductoController::class, 'index'])
    ->name('variantes.index');
Route::post('variantes', [VarianteProductoController::class, 'store'])->name('variantes.store');
Route::get('variantes/{variante}/edit', [VarianteProductoController::class, 'edit'])->name('variantes.edit');
Route::put('variantes/{variante}', [VarianteProductoController::class, 'update'])->name('variantes.update');
Route::delete('variantes/{variante}', [VarianteProductoController::class, 'destroy'])->name('variantes.destroy');
