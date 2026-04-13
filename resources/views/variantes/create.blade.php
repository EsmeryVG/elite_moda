@extends('layouts.app')

@section('page_title', 'Crear variante')
@section('page_subtitle', 'Agrega una nueva variante al producto seleccionado')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="mb-1">Nueva variante</h5>
                    <p class="text-muted mb-0">
                        Producto: <span class="fw-semibold">{{ $producto->nombre }}</span>
                    </p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('variantes.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="producto_id" value="{{ $producto->id }}">

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  id="descripcion"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Describe la variante">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="color" class="form-label">Color</label>
                            <select name="color" id="color" class="form-select" onchange="toggleNuevoCampo('color')">
                                <option value="">Seleccione un color</option>

                                @foreach($colores as $color)
                                    <option value="{{ $color }}" {{ old('color') == $color ? 'selected' : '' }}>
                                        {{ $color }}
                                    </option>
                                @endforeach

                                <option value="__otra__" {{ old('color') == '__otra__' ? 'selected' : '' }}>
                                    Otra...
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6" id="contenedorNuevoColor" style="display: none;">
                            <label for="nuevo_color" class="form-label">Nuevo color</label>
                            <input type="text"
                                   name="nuevo_color"
                                   id="nuevo_color"
                                   class="form-control"
                                   value="{{ old('nuevo_color') }}"
                                   placeholder="Escriba el nuevo color">
                        </div>

                        <div class="col-md-6">
                            <label for="talla" class="form-label">Talla</label>
                            <select name="talla" id="talla" class="form-select" onchange="toggleNuevoCampo('talla')">
                                <option value="">Seleccione una talla</option>

                                @foreach($tallas as $talla)
                                    <option value="{{ $talla }}" {{ old('talla') == $talla ? 'selected' : '' }}>
                                        {{ $talla }}
                                    </option>
                                @endforeach

                                <option value="__otra__" {{ old('talla') == '__otra__' ? 'selected' : '' }}>
                                    Otra...
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6" id="contenedorNuevaTalla" style="display: none;">
                            <label for="nueva_talla" class="form-label">Nueva talla</label>
                            <input type="text"
                                   name="nueva_talla"
                                   id="nueva_talla"
                                   class="form-control"
                                   value="{{ old('nueva_talla') }}"
                                   placeholder="Escriba la nueva talla">
                        </div>

                        <div class="col-md-6">
                            <label for="material" class="form-label">Material</label>
                            <select name="material" id="material" class="form-select" onchange="toggleNuevoCampo('material')">
                                <option value="">Seleccione un material</option>

                                @foreach($materiales as $material)
                                    <option value="{{ $material }}" {{ old('material') == $material ? 'selected' : '' }}>
                                        {{ $material }}
                                    </option>
                                @endforeach

                                <option value="__otra__" {{ old('material') == '__otra__' ? 'selected' : '' }}>
                                    Otra...
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6" id="contenedorNuevoMaterial" style="display: none;">
                            <label for="nuevo_material" class="form-label">Nuevo material</label>
                            <input type="text"
                                   name="nuevo_material"
                                   id="nuevo_material"
                                   class="form-control"
                                   value="{{ old('nuevo_material') }}"
                                   placeholder="Escriba el nuevo material">
                        </div>

                        <div class="col-md-6">
                            <label for="precio_venta" class="form-label">Precio de venta</label>
                            <input type="number"
                                   step="0.01"
                                   name="precio_venta"
                                   id="precio_venta"
                                   class="form-control"
                                   value="{{ old('precio_venta') }}"
                                   placeholder="Ej: 1200.00">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('productos.show', $producto) }}" class="btn btn-secondary">
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    function toggleNuevoCampo(tipo) {
        const select = document.getElementById(tipo);

        if (tipo === 'color') {
            document.getElementById('contenedorNuevoColor').style.display =
                select.value === '__otra__' ? 'block' : 'none';
        }

        if (tipo === 'talla') {
            document.getElementById('contenedorNuevaTalla').style.display =
                select.value === '__otra__' ? 'block' : 'none';
        }

        if (tipo === 'material') {
            document.getElementById('contenedorNuevoMaterial').style.display =
                select.value === '__otra__' ? 'block' : 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleNuevoCampo('color');
        toggleNuevoCampo('talla');
        toggleNuevoCampo('material');
    });
</script>