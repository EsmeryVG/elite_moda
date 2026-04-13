@extends('layouts.app')

@section('page_title', 'Editar producto')
@section('page_subtitle', 'Actualiza la información del producto')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="mb-1">Editar producto</h5>
                    <p class="text-muted mb-0">Modifica los datos del producto y guarda los cambios.</p>
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

                <form action="{{ route('productos.update', $producto) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text"
                               name="nombre"
                               id="nombre"
                               class="form-control"
                               value="{{ old('nombre', $producto->nombre) }}">
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  id="descripcion"
                                  class="form-control"
                                  rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                    </div>

                    <div class="mb-3">
    <label for="marca" class="form-label">Marca</label>
    <select name="marca" id="marca" class="form-select" onchange="toggleNuevaMarca()">
        <option value="">Seleccione una marca</option>

        @foreach($marcas as $marca)
            <option value="{{ $marca }}"
                {{ old('marca', in_array($producto->marca, $marcas->toArray()) ? $producto->marca : '__otra__') == $marca ? 'selected' : '' }}>
                {{ $marca }}
            </option>
        @endforeach

        <option value="__otra__"
            {{ old('marca', in_array($producto->marca, $marcas->toArray()) ? $producto->marca : '__otra__') == '__otra__' ? 'selected' : '' }}>
            Otra...
        </option>
    </select>
</div>

<div class="mb-3" id="contenedorNuevaMarca" style="display: none;">
    <label for="nueva_marca" class="form-label">Nueva marca</label>
    <input type="text"
           name="nueva_marca"
           id="nueva_marca"
           class="form-control"
           value="{{ old('nueva_marca', in_array($producto->marca, $marcas->toArray()) ? '' : $producto->marca) }}"
           placeholder="Escriba la nueva marca">
</div>

                    <div class="mb-4">
                        <label for="categoria_id" class="form-label">Categoría</label>
                        <select name="categoria_id" id="categoria_id" class="form-select">
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    function toggleNuevaMarca() {
        const selectMarca = document.getElementById('marca');
        const contenedorNuevaMarca = document.getElementById('contenedorNuevaMarca');

        if (selectMarca.value === '__otra__') {
            contenedorNuevaMarca.style.display = 'block';
        } else {
            contenedorNuevaMarca.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleNuevaMarca();
    });
</script>