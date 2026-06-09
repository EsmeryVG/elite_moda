@extends('layouts.app')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Recepciones de mercancía</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    {{ $recepciones->total() }} recepciones registradas
                </p>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

            <div style="position:relative; flex:1; max-width:280px;">
                <i class="bi bi-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                <input type="text" id="buscadorRecepciones" class="form-control"
                       placeholder="Buscar por código..."
                       style="padding-left:36px;"
                       value="{{ request('buscar') }}" autocomplete="off">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-sm em-filtro {{ !request('tipo') ? 'active' : '' }}"
                        data-tipo="">Todas</button>
                <button class="btn btn-sm em-filtro {{ request('tipo') === 'completa' ? 'active' : '' }}"
                        data-tipo="completa">Completa</button>
                <button class="btn btn-sm em-filtro {{ request('tipo') === 'parcial' ? 'active' : '' }}"
                        data-tipo="parcial">Parcial</button>
                <button class="btn btn-sm em-filtro {{ request('tipo') === 'no_conforme' ? 'active' : '' }}"
                        data-tipo="no_conforme">No conforme</button>
            </div>

        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:110px;">Código</th>
                        <th style="width:110px;">Orden</th>
                        <th>Proveedor</th>
                        <th>Almacén</th>
                        <th style="width:110px;">Fecha</th>
                        <th style="width:120px;">Tipo</th>
                        <th style="width:80px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaContainer" data-url="{{ route('recepciones.index') }}">
                    @include('recepciones._tabla')
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/ordenes_compra.css'])
@endpush

@push('scripts')
    @vite(['resources/js/recepciones.js'])
@endpush