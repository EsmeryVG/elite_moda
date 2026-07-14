@extends('layouts.app')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Notas de Crédito</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    {{ $notas->total() }} notas registradas
                </p>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

            <div style="position:relative; flex:1; max-width:280px;">
                <i class="bi bi-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                <input type="text" id="buscadorNotasCredito" class="form-control"
                       placeholder="Buscar por código, NCF o cliente..."
                       style="padding-left:36px;"
                       value="{{ request('busqueda') }}" autocomplete="off">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}" data-estado="">Todas</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'activa' ? 'active' : '' }}" data-estado="activa">Activas</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'agotada' ? 'active' : '' }}" data-estado="agotada">Agotadas</button>
            </div>

        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:110px;">Código</th>
                        <th style="width:130px;">NCF</th>
                        <th style="width:100px;">Fecha</th>
                        <th>Cliente</th>
                        <th>Venta original</th>
                        <th style="text-align:right; width:110px;">Monto original</th>
                        <th style="text-align:right; width:110px;">Disponible</th>
                        <th style="width:100px;">Estado</th>
                        <th style="width:70px;" class="text-end">Ver</th>
                    </tr>
                </thead>
                <tbody id="tablaContainer" data-url="{{ route('notas_credito.tabla') }}">
                    @include('notas_credito._tabla')
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
    @vite(['resources/js/notas_credito.js'])
@endpush