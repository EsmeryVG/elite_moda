@extends('layouts.app')

@section('content')
    <div class="card page-card w-100">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1 fw-semibold">Ventas</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        {{ $ventas->total() }} ventas registradas
                    </p>
                </div>
                @permiso('ventas.vender')
                    <a href="{{ route('ventas.create') }}" class="btn btn-primary">
                        <i class="bi bi-cart-plus me-1"></i> Nueva venta
                    </a>
                @endpermiso
            </div>

            <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

                <div style="position:relative; flex:1; max-width:280px;">
                    <i class="bi bi-search"
                        style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                    <input type="text" id="buscadorVentas" class="form-control" placeholder="Buscar por código o NCF..."
                        style="padding-left:36px;" value="{{ request('buscar') }}" autocomplete="off">
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}"
                        data-estado="">Todas</button>
                    <button class="btn btn-sm em-filtro {{ request('estado') === 'completada' ? 'active' : '' }}"
                        data-estado="completada">Completadas</button>
                    <button class="btn btn-sm em-filtro {{ request('estado') === 'anulada' ? 'active' : '' }}"
                        data-estado="anulada">Anuladas</button>
                </div>

            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width:110px;">Código</th>
                            <th style="width:130px;">Fecha</th>
                            <th>Cliente</th>
                            <th style="width:140px;">Vendedor</th>
                            <th style="width:130px;">NCF</th>
                            <th style="width:110px;">Estado</th>
                            <th style="width:120px;" class="text-end">Total</th>
                            <th style="width:80px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaContainer" data-url="{{ route('ventas.index') }}">
                        @include('ventas._tabla')
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/ventas.css'])
@endpush

@push('scripts')
    @vite(['resources/js/ventas.js'])
@endpush
