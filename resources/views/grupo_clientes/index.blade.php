@extends('layouts.app')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Grupos de cliente</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    {{ $grupos->count() }} grupos registrados
                </p>
            </div>
            <a href="{{ route('grupo_clientes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nuevo grupo
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th style="width:130px; text-align:center;">Descuento base</th>
                        <th style="width:100px; text-align:center;">Clientes</th>
                        <th style="width:100px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grupos as $grupo)
                        <tr>
                            <td class="fw-semibold">{{ $grupo->nombre }}</td>
                            <td style="font-size:13px; color:var(--text-muted);">
                                {{ $grupo->descripcion ?? '—' }}
                            </td>
                            <td class="text-center">
                                @if($grupo->descuento_base > 0)
                                    <span class="badge rounded-pill"
                                          style="background:rgba(211,47,47,0.10);
                                                 color:var(--accent); font-size:12px;
                                                 padding:4px 10px;">
                                        {{ $grupo->descuento_base }}%
                                    </span>
                                @else
                                    <span style="color:var(--text-muted); font-size:13px;">—</span>
                                @endif
                            </td>
                            <td class="text-center" style="font-size:13px; color:var(--text-muted);">
                                {{ $grupo->clientes_count }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('grupo_clientes.edit', $grupo) }}"
                                   class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if($grupo->clientes_count === 0)
                                    <form action="{{ route('grupo_clientes.destroy', $grupo) }}"
                                          method="POST" class="d-inline-block">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm"
                                                title="Eliminar"
                                                onclick="return confirm('¿Eliminar {{ $grupo->nombre }}?')">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5"
                                style="color:var(--text-muted);">
                                <i class="bi bi-inbox"
                                   style="font-size:28px; display:block; margin-bottom:8px;"></i>
                                No hay grupos registrados.
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
    @vite(['resources/css/clientes.css'])
@endpush