<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VarianteProductoController;
use App\Http\Controllers\AtributoController;

Route::get('/', function () {
    return view('inicio');
})->name('home');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Categorías
Route::resource('categorias', CategoriaController::class);
Route::patch('categorias/{categoria}/reactivar', [CategoriaController::class, 'reactivar'])
    ->name('categorias.reactivar');

// Atributos y valores
Route::resource('atributos', AtributoController::class);
Route::patch('atributos/{atributo}/reactivar', [AtributoController::class, 'reactivar'])
    ->name('atributos.reactivar');
Route::post('atributos/{atributo}/valores', [AtributoController::class, 'storeValor'])
    ->name('atributos.valores.store');
Route::patch('atributos/{atributo}/valores/{valor}', [AtributoController::class, 'updateValor'])
    ->name('atributos.valores.update');
Route::delete('atributos/{atributo}/valores/{valor}', [AtributoController::class, 'destroyValor'])
    ->name('atributos.valores.destroy');
Route::patch('atributos/{atributo}/valores/{valor}/reactivar', [AtributoController::class, 'reactivarValor'])
    ->name('atributos.valores.reactivar');

// Productos
Route::resource('productos', ProductoController::class);
Route::patch('productos/{producto}/desactivar', [ProductoController::class, 'desactivar'])
    ->name('productos.desactivar');
Route::patch('productos/{producto}/reactivar', [ProductoController::class, 'reactivar'])
    ->name('productos.reactivar');

// Variantes (solo edición individual desde show)
Route::post('variantes', [VarianteProductoController::class, 'store'])
    ->name('variantes.store');
Route::put('variantes/{variante}', [VarianteProductoController::class, 'update'])
    ->name('variantes.update');
Route::delete('variantes/{variante}', [VarianteProductoController::class, 'destroy'])
    ->name('variantes.destroy');
Route::patch('variantes/{variante}/desactivar', [VarianteProductoController::class, 'desactivar'])
    ->name('variantes.desactivar');
Route::patch('variantes/{variante}/reactivar', [VarianteProductoController::class, 'reactivar'])
    ->name('variantes.reactivar');
Route::get('variantes/{variante}/editar', [VarianteProductoController::class, 'editarForm'])
    ->name('variantes.editar.form');