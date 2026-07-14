@extends('layouts.app')

@section('page_title', 'Registrar Gasto Directo')
@section('page_subtitle', 'Gasto pagado sin usar caja chica (cheque, transferencia, etc.)')

@section('content')
    <div class="row g-4 justify-content-center">
        <div class="col-lg-6">
            <form action="{{ route('gastos.store') }}" method="POST">
                @csrf

                <div class="card page-card mb-4">
                    <div class="card-body p-4">

                        <div class="mb-3">
                            <label class="form-label">Nombre / Concepto <span style="color:var(--accent);">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre') }}" placeholder="Ej: Pago a proveedor de bolsas">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Categoría <span style="color:var(--accent);">*</span></label>
                            <select id="selectCategoriaGasto" name="categoria_gasto_id"
                                placeholder="Busca o crea una categoría...">
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        {{ old('categoria_gasto_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_gasto_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Monto <span style="color:var(--accent);">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"
                                    style="background:var(--bg-elevated); border-color:var(--border); color:var(--text-muted);">RD$</span>
                                <input type="number" name="monto" step="0.01" min="0.01"
                                    class="form-control @error('monto') is-invalid @enderror" value="{{ old('monto') }}">
                            </div>
                            @error('monto')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Método de pago <span style="color:var(--accent);">*</span></label>
                            <select name="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror">
                                <option value="">Selecciona un método</option>
                                <option value="cheque" {{ old('metodo_pago') === 'cheque' ? 'selected' : '' }}>Cheque
                                </option>
                                <option value="transferencia"
                                    {{ old('metodo_pago') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="efectivo" {{ old('metodo_pago') === 'efectivo' ? 'selected' : '' }}>Efectivo
                                    (fuera de caja)</option>
                                <option value="tarjeta" {{ old('metodo_pago') === 'tarjeta' ? 'selected' : '' }}>Tarjeta
                                </option>
                            </select>
                            @error('metodo_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Referencia (opcional)</label>
                            <input type="text" name="referencia" class="form-control" value="{{ old('referencia') }}"
                                placeholder="Nro. de cheque, confirmación de transferencia...">
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Observaciones (opcional)</label>
                            <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Registrar gasto
                    </button>
                    <a href="{{ route('gastos.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/gastos.css'])
@endpush

@push('scripts')
    @vite(['resources/js/gastos.js'])
@endpush
