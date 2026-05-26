@extends('layouts.app')

@section('page_title', 'Inicio')
@section('page_subtitle', 'Sistema de gestión para Tienda Elite Moda')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card page-card">
            <div class="card-body p-5">
                <div class="text-center mb-5">
                    <div class="mb-3">
                        <i class="bi bi-bag-heart-fill" style="font-size: 3rem; color: #1f3c88;"></i>
                    </div>
                    <h2 class="fw-bold mb-2">CRUD de Producto, Categoría y Variante</h2>
                    <p class="text-muted mb-0">
                        Sistema base para la administración del catálogo de la tienda.
                    </p>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 rounded-4">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-tags-fill mb-3" style="font-size: 2rem; color: #1f3c88;"></i>
                                <h5 class="fw-semibold">Categorías</h5>
                                <p class="text-muted mb-0">
                                    Registra y organiza las categorías de los productos.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 rounded-4">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-box-seam-fill mb-3" style="font-size: 2rem; color: #1f3c88;"></i>
                                <h5 class="fw-semibold">Productos</h5>
                                <p class="text-muted mb-0">
                                    Administra los productos con su categoría y marca.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 rounded-4">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-palette-fill mb-3" style="font-size: 2rem; color: #1f3c88;"></i>
                                <h5 class="fw-semibold">Variantes</h5>
                                <p class="text-muted mb-0">
                                    Gestiona color, talla, material y precio de cada producto.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-light rounded-4 p-4 text-center">
                    <h5 class="fw-semibold mb-2">Próximamente</h5>
                    <p class="text-muted mb-0">
                        Sistema de gestión completo para Tienda Elite Moda, con más módulos administrativos y funcionalidades de control.
                    </p>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                    <a href="{{ route('categorias.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-tags me-1"></i> Ir a categorías
                    </a>

                    <a href="{{ route('productos.index') }}" class="btn btn-primary">
                        <i class="bi bi-box me-1"></i> Ir a productos
                    </a>

                    <a href="{{ route('productos.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-palette me-1"></i> Ir a variantes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection