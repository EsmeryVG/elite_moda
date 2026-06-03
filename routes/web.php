<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VarianteProductoController;
use App\Http\Controllers\AtributoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\GrupoClienteController;
use App\Http\Controllers\OrdenCompraController;

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

// Sucursales
Route::resource('sucursales', SucursalController::class);
Route::patch('sucursales/{sucursal}/reactivar', [SucursalController::class, 'reactivar'])
    ->name('sucursales.reactivar');

// Almacenes
Route::resource('almacenes', AlmacenController::class);
Route::patch('almacenes/{almacen}/reactivar', [AlmacenController::class, 'reactivar'])
    ->name('almacenes.reactivar');

// Proveedores
Route::resource('proveedores', ProveedorController::class)->except(['show']);
Route::get('proveedores/{proveedor}', [ProveedorController::class, 'show'])
    ->name('proveedores.show');
Route::patch('proveedores/{proveedor}/reactivar', [ProveedorController::class, 'reactivar'])
    ->name('proveedores.reactivar');

// Clientes
Route::resource('clientes', ClienteController::class)->except(['show']);
Route::get('clientes/{cliente}', [ClienteController::class, 'show'])
    ->name('clientes.show');
Route::patch('clientes/{cliente}/reactivar', [ClienteController::class, 'reactivar'])
    ->name('clientes.reactivar');

// Grupos de clientes
Route::resource('grupo_clientes', GrupoClienteController::class)
    ->except(['show']);

// Órdenes de compra
Route::resource('ordenes_compra', OrdenCompraController::class)->except(['destroy']);
Route::patch('ordenes_compra/{ordenes_compra}/enviar',  [OrdenCompraController::class, 'enviar'])
    ->name('ordenes_compra.enviar');
Route::patch('ordenes_compra/{ordenes_compra}/cancelar', [OrdenCompraController::class, 'cancelar'])
    ->name('ordenes_compra.cancelar');
Route::get('api/variantes/buscar', [OrdenCompraController::class, 'buscarVariantes'])
    ->name('api.variantes.buscar');