@extends('layouts.app')

@section('content')
    <div class="card page-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="prod-section-title mb-0">Gastos fijos de {{ now()->translatedFormat('F Y') }}</p>
                @if (Auth::user()->esAdministrador())
                    <a href="{{ route('gastos_fijos.index') }}" style="font-size:12px;">Gestionar gastos fijos</a>
                @endif
            </div>
            @forelse($gastosFijos as $gf)
                <form action="{{ route('gastos.fijos.registrar', $gf['id']) }}" method="POST"
                    class="d-flex align-items-center gap-2 mb-2">
                    @csrf
                    <div style="flex:1; font-size:13px;">
                        {{ $gf['nombre'] }}
                        <span style="color:var(--text-muted); font-size:11.5px;">({{ $gf['categoria'] }})</span>
                    </div>
                    <input type="number" name="monto" step="0.01" class="form-control form-control-sm"
                        style="width:120px;" value="{{ $gf['monto_sugerido'] }}" {{ $gf['pagado'] ? 'disabled' : '' }}>
                    <button type="submit" class="btn btn-sm {{ $gf['pagado'] ? 'btn-outline-success' : 'btn-primary' }}"
                        {{ $gf['pagado'] ? 'disabled' : '' }}>
                        @if ($gf['pagado'])
                            <i class="bi bi-check2"></i> Pagado {{ $gf['fecha_pago'] }}
                        @else
                            Registrar pago
                        @endif
                    </button>
                </form>
            @empty
                <p class="text-muted" style="font-size:12.5px;">No hay gastos fijos configurados.</p>
            @endforelse
        </div>
    </div>
    <div class="card page-card w-100">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1 fw-semibold">Gastos</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        {{ $gastos->total() }} gastos registrados
                    </p>
                </div>
                <a href="{{ route('gastos.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Registrar gasto directo
                </a>
            </div>

            <div class="d-flex justify-content-between align-items-center gap-3 mb-3 flex-wrap">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm em-filtro {{ !request('origen') ? 'active' : '' }}"
                        data-origen="">Todos</button>
                    <button class="btn btn-sm em-filtro {{ request('origen') === 'caja_chica' ? 'active' : '' }}"
                        data-origen="caja_chica">Caja chica</button>
                    <button class="btn btn-sm em-filtro {{ request('origen') === 'directo' ? 'active' : '' }}"
                        data-origen="directo">Directos</button>
                </div>

                <form id="formFiltrosFecha" class="d-flex gap-2 align-items-center">
                    <input type="date" id="filtroDesde" class="form-control form-control-sm"
                        value="{{ request('desde') }}">
                    <span style="color:var(--text-muted); font-size:12px;">a</span>
                    <input type="date" id="filtroHasta" class="form-control form-control-sm"
                        value="{{ request('hasta') }}">
                    <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width:100px;">Fecha</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th style="width:110px;">Origen</th>
                            <th style="width:130px;">Registrado por</th>
                            <th style="text-align:right; width:110px;">Monto</th>
                        </tr>
                    </thead>
                    <tbody id="tablaContainer" data-url="{{ route('gastos.tabla') }}">
                        @include('gastos._tabla')
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/gastos.css'])
@endpush

@push('scripts')
    @vite(['resources/js/gastos.js'])
@endpush
