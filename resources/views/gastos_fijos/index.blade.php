@extends('layouts.app')

@section('content')
    <div class="card page-card w-100">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1 fw-semibold">Gastos Fijos</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        Gastos recurrentes mensuales (alquiler, servicios, etc.)
                    </p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoGastoFijo">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo gasto fijo
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th class="text-end">Monto sugerido</th>
                            <th style="width:100px;">Estado</th>
                            <th style="width:80px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gastosFijos as $gf)
                            <tr>
                                <td style="font-size:13px; font-weight:500;">{{ $gf->nombre }}</td>
                                <td style="font-size:12.5px; color:var(--text-muted);">{{ $gf->categoria?->nombre }}</td>
                                <td class="text-end" style="font-size:13px;">RD$ {{ number_format($gf->monto_sugerido, 2) }}
                                </td>
                                <td>
                                    @if ($gf->estado)
                                        <span class="badge rounded-pill"
                                            style="background:rgba(76,175,80,0.12); color:#2e7d32; font-size:11px; padding:4px 10px;">Activo</span>
                                    @else
                                        <span class="badge rounded-pill"
                                            style="background:rgba(158,158,158,0.15); color:#757575; font-size:11px; padding:4px 10px;">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($gf->estado)
                                        <form action="{{ route('gastos_fijos.destroy', $gf) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm" title="Desactivar"
                                                onclick="return confirm('¿Desactivar {{ $gf->nombre }}?')">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5" style="color:var(--text-muted);">
                                    No hay gastos fijos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modalNuevoGastoFijo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('gastos_fijos.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title fw-semibold">Nuevo gasto fijo</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre <span style="color:var(--accent);">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre') }}" placeholder="Ej: Alquiler local">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoría <span style="color:var(--accent);">*</span></label>
                            <select id="selectCategoriaGastoFijo" name="categoria_gasto_id"
                                placeholder="Busca o crea una categoría...">
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_gasto_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Monto sugerido <span style="color:var(--accent);">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">RD$</span>
                                <input type="number" name="monto_sugerido" step="0.01" min="0"
                                    class="form-control @error('monto_sugerido') is-invalid @enderror"
                                    value="{{ old('monto_sugerido') }}">
                            </div>
                            @error('monto_sugerido')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
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
