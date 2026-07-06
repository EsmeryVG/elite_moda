@extends('layouts.app')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Tipos de pago</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    {{ $tiposPago->count() }} tipos registrados
                </p>
            </div>
            <a href="{{ route('tipos_pago.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nuevo tipo
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th style="width:100px; text-align:center;">Pagos</th>
                        <th style="width:100px;">Estado</th>
                        <th style="width:100px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tiposPago as $tipo)
                        <tr>
                            <td class="fw-semibold" style="font-size:13px;">
                                {{ $tipo->nombre }}
                            </td>
                            <td class="text-center" style="font-size:13px; color:var(--text-muted);">
                                {{ $tipo->pagos_count }}
                            </td>
                            <td>
                                @if($tipo->estado)
                                    <span class="badge rounded-pill"
                                          style="background:rgba(76,175,80,0.12); color:#2e7d32;
                                                 font-size:11px; padding:4px 10px;">
                                        Activo
                                    </span>
                                @else
                                    <span class="badge rounded-pill"
                                          style="background:rgba(158,158,158,0.15); color:#757575;
                                                 font-size:11px; padding:4px 10px;">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('tipos_pago.edit', $tipo) }}"
                                   class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if($tipo->estado)
                                    <form action="{{ route('tipos_pago.destroy', $tipo) }}"
                                          method="POST" class="d-inline-block">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm"
                                                title="Desactivar"
                                                onclick="return confirm('¿Desactivar {{ $tipo->nombre }}?')">
                                            <i class="bi bi-toggle-on"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('tipos_pago.reactivar', $tipo) }}"
                                          method="POST" class="d-inline-block">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-outline-success btn-sm"
                                                title="Reactivar"
                                                onclick="return confirm('¿Reactivar {{ $tipo->nombre }}?')">
                                            <i class="bi bi-toggle-off"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5" style="color:var(--text-muted);">
                                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                                No hay tipos de pago registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/usuarios.css'])
@endpush