@extends('layouts.app')

@section('page_title', 'Roles')
@section('page_subtitle', 'Administra los roles del sistema')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Roles</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    {{ $roles->count() }} roles registrados
                </p>
            </div>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nuevo rol
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th style="width:100px; text-align:center;">Usuarios</th>
                        <th style="width:100px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $rol)
                        <tr>
                            <td>
                                <span class="fw-semibold" style="font-size:13px;">
                                    {{ $rol->nombre }}
                                </span>
                            </td>
                            <td style="font-size:13px; color:var(--text-muted);">
                                {{ $rol->descripcion ?? '—' }}
                            </td>
                            <td class="text-center" style="font-size:13px; color:var(--text-muted);">
                                {{ $rol->usuarios_count }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('roles.edit', $rol) }}"
                                   class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if($rol->usuarios_count === 0)
                                    <form action="{{ route('roles.destroy', $rol) }}"
                                          method="POST" class="d-inline-block">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm"
                                                title="Eliminar"
                                                onclick="return confirm('¿Eliminar {{ $rol->nombre }}?')">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5"
                                style="color:var(--text-muted);">
                                <i class="bi bi-inbox"
                                   style="font-size:28px; display:block; margin-bottom:8px;"></i>
                                No hay roles registrados.
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