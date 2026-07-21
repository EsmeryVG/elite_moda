<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VarianteProductoController;
use App\Http\Controllers\AtributoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\GrupoClienteController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\RecepcionMercanciaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\AjusteInventarioController;
use App\Http\Controllers\TipoPagoController;
use App\Http\Controllers\ComprobanteFiscalController;
use App\Http\Controllers\DescuentoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\SesionCajaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CajaChicaController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\GastoFijoController;
use App\Http\Controllers\CategoriaGastoController;
use App\Http\Controllers\NotaCreditoController;
use App\Http\Controllers\CuadreDiarioController;
use App\Http\Controllers\CuentaPorCobrarController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ReporteController;

// ── Rutas públicas (sin auth) ────────────────────
Route::get('login',   [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login',  [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// ── Rutas protegidas ─────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/', fn() => redirect()->route('home'));
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('api/dashboard/grafico-datos', [HomeController::class, 'graficoDatos'])->name('api.dashboard.grafico_datos');

    // ══════════════ CATÁLOGO (productos.ver / productos.gestionar) ══════════════
    // NOTA: rutas literales (/create, index) primero; wildcards ({id}) SIEMPRE al final del bloque del módulo.

    Route::middleware('permiso:productos.ver')->group(function () {
        Route::get('categorias', [CategoriaController::class, 'index'])->name('categorias.index');
        Route::get('atributos', [AtributoController::class, 'index'])->name('atributos.index');
        Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('descuentos', [DescuentoController::class, 'index'])->name('descuentos.index');
    });

    Route::middleware('permiso:productos.gestionar')->group(function () {
        Route::get('categorias/create', [CategoriaController::class, 'create'])->name('categorias.create');
        Route::post('categorias', [CategoriaController::class, 'store'])->name('categorias.store');
        Route::get('categorias/{categoria}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
        Route::put('categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
        Route::delete('categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
        Route::patch('categorias/{categoria}/reactivar', [CategoriaController::class, 'reactivar'])->name('categorias.reactivar');

        Route::post('atributos', [AtributoController::class, 'store'])->name('atributos.store');
        Route::put('atributos/{atributo}', [AtributoController::class, 'update'])->name('atributos.update');
        Route::delete('atributos/{atributo}', [AtributoController::class, 'destroy'])->name('atributos.destroy');
        Route::patch('atributos/{atributo}/reactivar', [AtributoController::class, 'reactivar'])->name('atributos.reactivar');
        Route::post('atributos/{atributo}/valores', [AtributoController::class, 'storeValor'])->name('atributos.valores.store');
        Route::patch('atributos/{atributo}/valores/{valor}', [AtributoController::class, 'updateValor'])->name('atributos.valores.update');
        Route::delete('atributos/{atributo}/valores/{valor}', [AtributoController::class, 'destroyValor'])->name('atributos.valores.destroy');
        Route::patch('atributos/{atributo}/valores/{valor}/reactivar', [AtributoController::class, 'reactivarValor'])->name('atributos.valores.reactivar');

        Route::get('productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
        Route::patch('productos/{producto}/desactivar', [ProductoController::class, 'desactivar'])->name('productos.desactivar');
        Route::patch('productos/{producto}/reactivar', [ProductoController::class, 'reactivar'])->name('productos.reactivar');

        Route::post('variantes', [VarianteProductoController::class, 'store'])->name('variantes.store');
        Route::put('variantes/{variante}', [VarianteProductoController::class, 'update'])->name('variantes.update');
        Route::delete('variantes/{variante}', [VarianteProductoController::class, 'destroy'])->name('variantes.destroy');
        Route::patch('variantes/{variante}/desactivar', [VarianteProductoController::class, 'desactivar'])->name('variantes.desactivar');
        Route::patch('variantes/{variante}/reactivar', [VarianteProductoController::class, 'reactivar'])->name('variantes.reactivar');
        Route::get('variantes/{variante}/editar', [VarianteProductoController::class, 'editarForm'])->name('variantes.editar.form');

        Route::get('descuentos/create', [DescuentoController::class, 'create'])->name('descuentos.create');
        Route::post('descuentos', [DescuentoController::class, 'store'])->name('descuentos.store');
        Route::get('descuentos/{descuento}/edit', [DescuentoController::class, 'edit'])->name('descuentos.edit');
        Route::put('descuentos/{descuento}', [DescuentoController::class, 'update'])->name('descuentos.update');
        Route::delete('descuentos/{descuento}', [DescuentoController::class, 'destroy'])->name('descuentos.destroy');
        Route::patch('descuentos/{descuento}/reactivar', [DescuentoController::class, 'reactivar'])->name('descuentos.reactivar');
    });

    // Wildcards {id} de catálogo — SIEMPRE al final del módulo
    Route::middleware('permiso:productos.ver')->group(function () {
        Route::get('categorias/{categoria}', [CategoriaController::class, 'show'])->name('categorias.show');
        Route::get('productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
        Route::get('descuentos/{descuento}', [DescuentoController::class, 'show'])->name('descuentos.show');
    });

    Route::get('api/descuentos/calcular', [DescuentoController::class, 'calcular'])->name('api.descuentos.calcular');

    // ══════════════ INVENTARIO (inventario.ver / inventario.gestionar / almacenes.ver) ══════════════
    Route::middleware('permiso:inventario.ver')->group(function () {
        Route::get('inventario/stock', [StockController::class, 'index'])->name('stock.index');
        Route::get('inventario/movimientos', [MovimientoInventarioController::class, 'index'])->name('movimientos.index');
        Route::get('ajustes', [AjusteInventarioController::class, 'index'])->name('ajustes.index');
    });
    Route::middleware('permiso:inventario.gestionar')->group(function () {
        Route::get('ajustes/create', [AjusteInventarioController::class, 'create'])->name('ajustes.create');
        Route::post('ajustes', [AjusteInventarioController::class, 'store'])->name('ajustes.store');
        Route::patch('ajustes/{ajuste}/aprobar', [AjusteInventarioController::class, 'aprobar'])->name('ajustes.aprobar');
        Route::patch('ajustes/{ajuste}/rechazar', [AjusteInventarioController::class, 'rechazar'])->name('ajustes.rechazar');
    });
    Route::middleware('permiso:inventario.ver')->group(function () {
        Route::get('ajustes/{ajuste}', [AjusteInventarioController::class, 'show'])->name('ajustes.show');
    });
    Route::get('api/stock/buscar', [AjusteInventarioController::class, 'buscarStockVariante'])->name('api.stock.buscar');

    Route::middleware('permiso:almacenes.ver')->group(function () {
    Route::get('almacenes', [AlmacenController::class, 'index'])->name('almacenes.index');
});
Route::middleware('permiso:productos.gestionar')->group(function () {
    Route::get('almacenes/create', [AlmacenController::class, 'create'])->name('almacenes.create');
    Route::post('almacenes', [AlmacenController::class, 'store'])->name('almacenes.store');
    Route::get('almacenes/{almacen}/edit', [AlmacenController::class, 'edit'])->name('almacenes.edit');
    Route::put('almacenes/{almacen}', [AlmacenController::class, 'update'])->name('almacenes.update');
    Route::delete('almacenes/{almacen}', [AlmacenController::class, 'destroy'])->name('almacenes.destroy');
    Route::patch('almacenes/{almacen}/reactivar', [AlmacenController::class, 'reactivar'])->name('almacenes.reactivar');
});


    // ══════════════ VENTAS (ventas.vender / ventas.ver / ventas.anular) ══════════════
    Route::middleware(['sesion.caja', 'permiso:ventas.vender'])->group(function () {
        Route::get('ventas/create', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');
    });
    Route::middleware('permiso:ventas.ver')->group(function () {
        Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
        Route::get('ventas/{venta}/factura', [VentaController::class, 'factura'])->name('ventas.factura');
    });
    Route::patch('ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular')->middleware('permiso:ventas.anular');
    Route::get('api/productos/buscar', [VentaController::class, 'buscarProductos'])->name('api.productos.buscar');
    Route::get('api/empleados/buscar', [VentaController::class, 'buscarEmpleados'])->name('api.empleados.buscar');
    Route::get('api/clientes/buscar', [VentaController::class, 'buscarClientes'])->name('api.clientes.buscar');
    Route::get('api/tpv/categorias', [VentaController::class, 'categorias'])->name('api.tpv.categorias');
    Route::get('api/tpv/productos-categoria', [VentaController::class, 'productosPorCategoria'])->name('api.tpv.productos.categoria');
    Route::get('api/clientes/credito', [VentaController::class, 'verificarCredito'])->name('api.clientes.credito');
    Route::get('api/ventas/notas-credito-cliente', [VentaController::class, 'notasCreditoCliente'])->name('api.ventas.notas_credito_cliente');

    // ══════════════ DEVOLUCIONES (devoluciones.gestionar / devoluciones.ver) ══════════════
    Route::middleware('permiso:devoluciones.ver')->group(function () {
        Route::get('devoluciones', [DevolucionController::class, 'index'])->name('devoluciones.index');
        Route::get('devoluciones/tabla', [DevolucionController::class, 'tabla'])->name('devoluciones.tabla');
    });
    Route::middleware('permiso:devoluciones.gestionar')->group(function () {
        Route::get('devoluciones/buscar', [DevolucionController::class, 'buscarFactura'])->name('devoluciones.buscar');
        Route::get('devoluciones/crear/{venta}', [DevolucionController::class, 'create'])->name('devoluciones.create');
        Route::post('devoluciones', [DevolucionController::class, 'store'])->name('devoluciones.store');
        Route::post('devoluciones/detalle/{detalleDevolucion}/inspeccionar', [DevolucionController::class, 'inspeccionar'])->name('devoluciones.inspeccionar');
    });
    Route::middleware('permiso:devoluciones.ver')->group(function () {
        Route::get('devoluciones/{devolucion}', [DevolucionController::class, 'show'])->name('devoluciones.show');
    });
    Route::get('api/devoluciones/productos-cliente', [DevolucionController::class, 'buscarProductosCliente'])->name('api.devoluciones.productos_cliente');

    Route::middleware('permiso:notas_credito.ver')->group(function () {
        Route::get('notas-credito', [NotaCreditoController::class, 'index'])->name('notas_credito.index');
        Route::get('notas-credito/tabla', [NotaCreditoController::class, 'tabla'])->name('notas_credito.tabla');
        Route::get('notas-credito/{notaCredito}', [NotaCreditoController::class, 'show'])->name('notas_credito.show');
    });

    // ══════════════ CLIENTES (clientes.ver / clientes.gestionar) ══════════════
    Route::middleware('permiso:clientes.ver')->group(function () {
        Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('grupo_clientes', [GrupoClienteController::class, 'index'])->name('grupo_clientes.index');
    });
    Route::middleware('permiso:clientes.gestionar')->group(function () {
        Route::get('clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::patch('clientes/{cliente}/reactivar', [ClienteController::class, 'reactivar'])->name('clientes.reactivar');

        Route::get('grupo_clientes/create', [GrupoClienteController::class, 'create'])->name('grupo_clientes.create');
        Route::post('grupo_clientes', [GrupoClienteController::class, 'store'])->name('grupo_clientes.store');
        Route::get('grupo_clientes/{grupo_cliente}/edit', [GrupoClienteController::class, 'edit'])->name('grupo_clientes.edit');
        Route::put('grupo_clientes/{grupo_cliente}', [GrupoClienteController::class, 'update'])->name('grupo_clientes.update');
        Route::delete('grupo_clientes/{grupo_cliente}', [GrupoClienteController::class, 'destroy'])->name('grupo_clientes.destroy');
    });
    Route::middleware('permiso:clientes.ver')->group(function () {
        Route::get('clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    });

    // ══════════════ CUENTAS POR COBRAR (cxc.ver / cxc.abonar) ══════════════
    Route::middleware('permiso:cxc.ver')->group(function () {
        Route::get('cuentas-por-cobrar', [CuentaPorCobrarController::class, 'index'])->name('cuentas_por_cobrar.index');
        Route::get('cuentas-por-cobrar/tabla', [CuentaPorCobrarController::class, 'tabla'])->name('cuentas_por_cobrar.tabla');
        Route::get('cuentas-por-cobrar/{cuentaPorCobrar}', [CuentaPorCobrarController::class, 'show'])->name('cuentas_por_cobrar.show');
    });
    Route::post('cuentas-por-cobrar/{cuentaPorCobrar}/abono', [CuentaPorCobrarController::class, 'registrarAbono'])
        ->name('cuentas_por_cobrar.abono')->middleware('permiso:cxc.abonar');

    // ══════════════ COMPRAS (compras.ver / compras.gestionar) ══════════════
    Route::middleware('permiso:compras.ver')->group(function () {
        Route::get('ordenes_compra', [OrdenCompraController::class, 'index'])->name('ordenes_compra.index');
        Route::get('recepciones', [RecepcionMercanciaController::class, 'index'])->name('recepciones.index');
        Route::get('proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
    });
    Route::middleware('permiso:compras.gestionar')->group(function () {
        Route::get('ordenes_compra/create', [OrdenCompraController::class, 'create'])->name('ordenes_compra.create');
        Route::post('ordenes_compra', [OrdenCompraController::class, 'store'])->name('ordenes_compra.store');
        Route::get('ordenes_compra/{ordenes_compra}/edit', [OrdenCompraController::class, 'edit'])->name('ordenes_compra.edit');
        Route::put('ordenes_compra/{ordenes_compra}', [OrdenCompraController::class, 'update'])->name('ordenes_compra.update');
        Route::patch('ordenes_compra/{ordenes_compra}/confirmar', [OrdenCompraController::class, 'confirmar'])->name('ordenes_compra.confirmar');
        Route::patch('ordenes_compra/{ordenes_compra}/cancelar', [OrdenCompraController::class, 'cancelar'])->name('ordenes_compra.cancelar');
        Route::patch('ordenes_compra/{ordenes_compra}/marcar-pagada', [OrdenCompraController::class, 'marcarPagada'])->name('ordenes_compra.marcar_pagada');

        Route::get('recepciones/create', [RecepcionMercanciaController::class, 'create'])->name('recepciones.create');
        Route::post('recepciones', [RecepcionMercanciaController::class, 'store'])->name('recepciones.store');

        Route::get('proveedores/create', [ProveedorController::class, 'create'])->name('proveedores.create');
        Route::post('proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
        Route::get('proveedores/{proveedor}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit');
        Route::put('proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::delete('proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
        Route::patch('proveedores/{proveedor}/reactivar', [ProveedorController::class, 'reactivar'])->name('proveedores.reactivar');
    });
    // Wildcards {id} de compras — SIEMPRE al final del módulo
    Route::middleware('permiso:compras.ver')->group(function () {
        Route::get('ordenes_compra/{ordenes_compra}', [OrdenCompraController::class, 'show'])->name('ordenes_compra.show');
        Route::get('recepciones/{recepcion}', [RecepcionMercanciaController::class, 'show'])->name('recepciones.show');
        Route::get('proveedores/{proveedor}', [ProveedorController::class, 'show'])->name('proveedores.show');
    });
    Route::get('api/variantes/buscar', [OrdenCompraController::class, 'buscarVariantes'])->name('api.variantes.buscar');

// ══════════════ CAJA (caja.ver / caja.abrir / caja.autorizar_descuadre) ══════════════
    Route::middleware('permiso:caja.ver')->group(function () {
        Route::get('cajas', [CajaController::class, 'index'])->name('cajas.index');
        Route::get('sesiones-caja', [SesionCajaController::class, 'index'])->name('sesiones_caja.index');
        Route::get('sesiones-caja/tabla', [SesionCajaController::class, 'tabla'])->name('sesiones_caja.tabla');
        Route::get('cuadre-diario', [CuadreDiarioController::class, 'index'])->name('cuadre_diario.index');
    });
    Route::middleware('permiso:caja.autorizar_descuadre')->group(function () {
        Route::get('sesiones-caja/pendientes-revision', [SesionCajaController::class, 'pendientesRevision'])->name('sesiones_caja.pendientes_revision');
        Route::patch('sesiones-caja/{sesionCaja}/marcar-revisada', [SesionCajaController::class, 'marcarRevisada'])->name('sesiones_caja.marcar_revisada');
    });
    Route::middleware('permiso:caja.abrir')->group(function () {
        Route::get('sesiones-caja/abrir', [SesionCajaController::class, 'formularioAbrir'])->name('sesiones_caja.abrir');
        Route::post('sesiones-caja/abrir', [SesionCajaController::class, 'abrir'])->name('sesiones_caja.abrir.store');
        Route::get('sesiones-caja/{sesionCaja}/cerrar', [SesionCajaController::class, 'formularioCerrar'])->name('sesiones_caja.cerrar');
        Route::post('sesiones-caja/{sesionCaja}/cerrar', [SesionCajaController::class, 'cerrar'])->name('sesiones_caja.cerrar.store');
        Route::get('cajas/create', [CajaController::class, 'create'])->name('cajas.create');
        Route::post('cajas', [CajaController::class, 'store'])->name('cajas.store');
        Route::get('cajas/{caja}/edit', [CajaController::class, 'edit'])->name('cajas.edit');
        Route::put('cajas/{caja}', [CajaController::class, 'update'])->name('cajas.update');
        Route::delete('cajas/{caja}', [CajaController::class, 'destroy'])->name('cajas.destroy');
        Route::patch('cajas/{caja}/reactivar', [CajaController::class, 'reactivar'])->name('cajas.reactivar');
    });
    // Wildcard {id} de caja — SIEMPRE al final del módulo
    Route::middleware('permiso:caja.ver')->group(function () {
        Route::get('sesiones-caja/{sesionCaja}', [SesionCajaController::class, 'show'])->name('sesiones_caja.show');
    });

    // ══════════════ CAJA CHICA / GASTOS ══════════════
    Route::middleware('permiso:caja_chica.ver')->group(function () {
        Route::get('caja-chica', [CajaChicaController::class, 'show'])->name('caja_chica.show');
    });
    Route::middleware('permiso:caja_chica.gestionar')->group(function () {
        Route::post('caja-chica/gasto', [CajaChicaController::class, 'registrarGasto'])->name('caja_chica.gasto');
        Route::post('caja-chica/reponer', [CajaChicaController::class, 'reponer'])->name('caja_chica.reponer');
        Route::post('caja-chica/reponer-extraordinaria', [CajaChicaController::class, 'reponerExtraordinaria'])->name('caja_chica.reponer_extraordinaria');
    });

    Route::middleware('permiso:gastos.ver')->group(function () {
        Route::get('gastos', [GastoController::class, 'index'])->name('gastos.index');
        Route::get('gastos/tabla', [GastoController::class, 'tabla'])->name('gastos.tabla');
        Route::get('gastos-fijos', [GastoFijoController::class, 'index'])->name('gastos_fijos.index');
    });
    Route::middleware('permiso:gastos.gestionar')->group(function () {
        Route::get('gastos/create', [GastoController::class, 'create'])->name('gastos.create');
        Route::post('gastos', [GastoController::class, 'store'])->name('gastos.store');
        Route::post('categorias-gasto', [CategoriaGastoController::class, 'store'])->name('categorias_gasto.store');
        Route::post('gastos-fijos', [GastoFijoController::class, 'store'])->name('gastos_fijos.store');
        Route::delete('gastos-fijos/{gastoFijo}', [GastoFijoController::class, 'destroy'])->name('gastos_fijos.destroy');
        Route::post('gastos/fijos/{gastoFijo}/registrar', [GastoController::class, 'registrarGastoFijo'])->name('gastos.fijos.registrar');
    });

    // ══════════════ PERSONAL (empleados.ver / nomina.ver / nomina.gestionar) ══════════════
    Route::middleware('permiso:empleados.ver')->group(function () {
        Route::get('empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    });
    Route::middleware('permiso:usuarios.gestionar')->group(function () {
        Route::get('empleados/create', [EmpleadoController::class, 'create'])->name('empleados.create');
        Route::post('empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
        Route::get('empleados/{empleado}/edit', [EmpleadoController::class, 'edit'])->name('empleados.edit');
        Route::put('empleados/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update');
        Route::delete('empleados/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
        Route::patch('empleados/{empleado}/reactivar', [EmpleadoController::class, 'reactivar'])->name('empleados.reactivar');
    });

    Route::middleware('permiso:nomina.ver')->group(function () {
        Route::get('nomina', [NominaController::class, 'index'])->name('nomina.index');
        Route::get('nomina/detalle/{detalleNomina}', [NominaController::class, 'detalleEmpleado'])->name('nomina.detalle_empleado');
    });
    Route::middleware('permiso:nomina.gestionar')->group(function () {
        Route::post('nomina/generar', [NominaController::class, 'generar'])->name('nomina.generar');
        Route::patch('nomina/{nomina}/marcar-pagada', [NominaController::class, 'marcarPagada'])->name('nomina.marcar_pagada');
    });
    Route::middleware('permiso:nomina.ver')->group(function () {
        Route::get('nomina/{nomina}', [NominaController::class, 'show'])->name('nomina.show');
    });

    // ══════════════ REPORTES (reportes.ver) ══════════════
    Route::middleware('permiso:reportes.ver')->group(function () {
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
    Route::get('reportes/ventas/csv', [ReporteController::class, 'ventasCsv'])->name('reportes.ventas.csv');
    Route::get('reportes/ventas/pdf', [ReporteController::class, 'ventasPdf'])->name('reportes.ventas.pdf');
    Route::get('reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
    Route::get('reportes/inventario/csv', [ReporteController::class, 'inventarioCsv'])->name('reportes.inventario.csv');
    Route::get('reportes/inventario/pdf', [ReporteController::class, 'inventarioPdf'])->name('reportes.inventario.pdf');
    Route::get('reportes/compras', [ReporteController::class, 'compras'])->name('reportes.compras');
    Route::get('reportes/compras/csv', [ReporteController::class, 'comprasCsv'])->name('reportes.compras.csv');
    Route::get('reportes/compras/pdf', [ReporteController::class, 'comprasPdf'])->name('reportes.compras.pdf');
    Route::get('reportes/credito', [ReporteController::class, 'credito'])->name('reportes.credito');
    Route::get('reportes/credito/csv', [ReporteController::class, 'creditoCsv'])->name('reportes.credito.csv');
    Route::get('reportes/credito/pdf', [ReporteController::class, 'creditoPdf'])->name('reportes.credito.pdf');
});

    // ══════════════ SISTEMA (solo admin en la práctica) ══════════════
    Route::middleware('permiso:usuarios.gestionar')->group(function () {
        Route::get('roles', [RolController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RolController::class, 'create'])->name('roles.create');
        Route::post('roles', [RolController::class, 'store'])->name('roles.store');
        Route::get('roles/{rol}/edit', [RolController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{rol}', [RolController::class, 'update'])->name('roles.update');
        Route::delete('roles/{rol}', [RolController::class, 'destroy'])->name('roles.destroy');

        Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
        Route::patch('usuarios/{usuario}/reactivar', [UsuarioController::class, 'reactivar'])->name('usuarios.reactivar');
    });

    Route::middleware('permiso:configuracion.gestionar')->group(function () {
        Route::get('sucursales', [SucursalController::class, 'index'])->name('sucursales.index');
        Route::get('sucursales/create', [SucursalController::class, 'create'])->name('sucursales.create');
        Route::post('sucursales', [SucursalController::class, 'store'])->name('sucursales.store');
        Route::get('sucursales/{sucursale}/edit', [SucursalController::class, 'edit'])->name('sucursales.edit');
        Route::put('sucursales/{sucursale}', [SucursalController::class, 'update'])->name('sucursales.update');
        Route::delete('sucursales/{sucursale}', [SucursalController::class, 'destroy'])->name('sucursales.destroy');
        Route::patch('sucursales/{sucursale}/reactivar', [SucursalController::class, 'reactivar'])->name('sucursales.reactivar');

        Route::get('comprobantes', [ComprobanteFiscalController::class, 'index'])->name('comprobantes.index');
        Route::get('comprobantes/create', [ComprobanteFiscalController::class, 'create'])->name('comprobantes.create');
        Route::post('comprobantes', [ComprobanteFiscalController::class, 'store'])->name('comprobantes.store');
        Route::patch('comprobantes/{comprobante}/desactivar', [ComprobanteFiscalController::class, 'desactivar'])->name('comprobantes.desactivar');
        Route::patch('comprobantes/{comprobante}/reactivar', [ComprobanteFiscalController::class, 'reactivar'])->name('comprobantes.reactivar');
        Route::get('comprobantes/{comprobante}/edit', [ComprobanteFiscalController::class, 'edit'])->name('comprobantes.edit');
        Route::put('comprobantes/{comprobante}', [ComprobanteFiscalController::class, 'update'])->name('comprobantes.update');

        Route::get('tipos_pago', [TipoPagoController::class, 'index'])->name('tipos_pago.index');
        Route::get('tipos_pago/create', [TipoPagoController::class, 'create'])->name('tipos_pago.create');
        Route::post('tipos_pago', [TipoPagoController::class, 'store'])->name('tipos_pago.store');
        Route::get('tipos_pago/{tipo_pago}/edit', [TipoPagoController::class, 'edit'])->name('tipos_pago.edit');
        Route::put('tipos_pago/{tipo_pago}', [TipoPagoController::class, 'update'])->name('tipos_pago.update');
        Route::delete('tipos_pago/{tipo_pago}', [TipoPagoController::class, 'destroy'])->name('tipos_pago.destroy');
        Route::patch('tipos_pago/{tipo_pago}/reactivar', [TipoPagoController::class, 'reactivar'])->name('tipos_pago.reactivar');

        Route::get('configuraciones', [ConfiguracionController::class, 'index'])->name('configuraciones.index');
        Route::post('configuraciones', [ConfiguracionController::class, 'update'])->name('configuraciones.update');
    });
    // Wildcards {id} de sistema — SIEMPRE al final del módulo
    Route::middleware('permiso:configuracion.gestionar')->group(function () {
        Route::get('sucursales/{sucursale}', [SucursalController::class, 'show'])->name('sucursales.show');
        Route::get('comprobantes/{comprobante}', [ComprobanteFiscalController::class, 'show'])->name('comprobantes.show');
    });

    // Perfil de usuario (cualquier usuario autenticado)
    Route::put('perfil/password', [PerfilController::class, 'actualizarPassword'])->name('perfil.password.update');
});