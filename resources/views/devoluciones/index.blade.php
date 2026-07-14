@extends('layouts.app')

@section('content')
    <div class="card page-card w-100">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1 fw-semibold">Devoluciones</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        Devoluciones y notas de crédito registradas
                    </p>
                </div>
                <a href="{{ route('devoluciones.buscar') }}" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Buscar factura sin recibo
                </a>
            </div>

            <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

                <div style="position:relative; flex:1; max-width:280px;">
                    <i class="bi bi-search"
                        style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                    <input type="text" id="buscadorDevoluciones" class="form-control"
                        placeholder="Buscar por código o cliente..." style="padding-left:36px;"
                        value="{{ request('busqueda') }}" autocomplete="off">
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}"
                        data-estado="">Todas</button>
                    <button class="btn btn-sm em-filtro {{ request('estado') === 'pendiente' ? 'active' : '' }}"
                        data-estado="pendiente">Pendiente</button>
                    <button class="btn btn-sm em-filtro {{ request('estado') === 'aprobada' ? 'active' : '' }}"
                        data-estado="aprobada">Aprobada</button>
                </div>

            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width:110px;">Código</th>
                            <th style="width:100px;">Fecha</th>
                            <th>Cliente</th>
                            <th>Venta</th>
                            <th style="width:110px;" class="text-end">Total NC</th>
                            <th style="width:120px;">Estado</th>
                            <th style="width:80px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaContainer" data-url="{{ route('devoluciones.tabla') }}">
                        @include('devoluciones._tabla')
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/devoluciones.css'])
@endpush

@push('scripts')
    @vite(['resources/js/devoluciones.js'])
@endpush
