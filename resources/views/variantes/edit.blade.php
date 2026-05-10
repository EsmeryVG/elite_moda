@extends('layouts.app')

@section('page_title', 'Editar variante')
@section('page_subtitle', 'Actualiza los atributos y valores de esta variante')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="mb-1">Editar variante</h5>
                    <p class="text-muted mb-0">
                        Producto asociado:
                        <span class="fw-semibold">{{ $variante->producto?->nombre }}</span>
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

                <form action="{{ route('variantes.update', $variante) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="producto_id" value="{{ $variante->producto_id }}">

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  id="descripcion"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Ej: Camiseta azul talla S">{{ old('descripcion', $variante->descripcion) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="precio_venta" class="form-label">Precio de venta</label>
                        <input type="number"
                               step="0.01"
                               name="precio_venta"
                               id="precio_venta"
                               class="form-control"
                               value="{{ old('precio_venta', $variante->precio_venta) }}"
                               placeholder="Ej: 1200.00">
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Atributos de la variante</h5>
                            <p class="text-muted mb-0">
                                Modifica los atributos y valores que componen esta variante.
                            </p>
                        </div>

                        <button type="button" class="btn btn-outline-primary" onclick="agregarFilaAtributo()">
                            <i class="bi bi-plus-circle me-1"></i> Agregar atributo
                        </button>
                    </div>

                    <div id="contenedorAtributos">
                        @php
                            $valoresFormulario = old('atributo_valor_ids', $valoresSeleccionados ?? []);
                        @endphp

                        @foreach($valoresFormulario as $valorSeleccionado)
                            @php
                                $atributoSeleccionado = null;

                                foreach ($atributos as $atributo) {
                                    foreach ($atributo->valores as $valor) {
                                        if ((int) $valor->id === (int) $valorSeleccionado) {
                                            $atributoSeleccionado = $atributo->id;
                                        }
                                    }
                                }
                            @endphp

                            <div class="row g-3 align-items-end mb-3 fila-atributo">
                                <div class="col-md-5">
                                    <label class="form-label">Atributo</label>
                                    <select class="form-select atributo-select" onchange="actualizarValores(this)">
                                        <option value="">Seleccione un atributo</option>
                                        @foreach($atributos as $atributo)
                                            <option value="{{ $atributo->id }}" {{ $atributoSeleccionado == $atributo->id ? 'selected' : '' }}>
                                                {{ $atributo->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label">Valor</label>
                                    <select name="atributo_valor_ids[]" class="form-select valor-select">
                                        <option value="">Seleccione un valor</option>

                                        @foreach($atributos as $atributo)
                                            @foreach($atributo->valores as $valor)
                                                <option value="{{ $valor->id }}"
                                                        data-atributo="{{ $atributo->id }}"
                                                        {{ (int) $valorSeleccionado === (int) $valor->id ? 'selected' : '' }}
                                                        style="{{ $atributoSeleccionado == $atributo->id ? '' : 'display:none;' }}">
                                                    {{ $valor->valor }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        @if(count($valoresFormulario) === 0)
                            <div class="row g-3 align-items-end mb-3 fila-atributo">
                                <div class="col-md-5">
                                    <label class="form-label">Atributo</label>
                                    <select class="form-select atributo-select" onchange="actualizarValores(this)">
                                        <option value="">Seleccione un atributo</option>
                                        @foreach($atributos as $atributo)
                                            <option value="{{ $atributo->id }}">
                                                {{ $atributo->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label">Valor</label>
                                    <select name="atributo_valor_ids[]" class="form-select valor-select">
                                        <option value="">Seleccione un valor</option>

                                        @foreach($atributos as $atributo)
                                            @foreach($atributo->valores as $valor)
                                                <option value="{{ $valor->id }}"
                                                        data-atributo="{{ $atributo->id }}"
                                                        style="display:none;">
                                                    {{ $valor->valor }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>


                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('productos.show', $variante->producto_id) }}" class="btn btn-secondary">
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Actualizar variante
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<template id="templateFilaAtributo">
    <div class="row g-3 align-items-end mb-3 fila-atributo">
        <div class="col-md-5">
            <label class="form-label">Atributo</label>
            <select class="form-select atributo-select" onchange="actualizarValores(this)">
                <option value="">Seleccione un atributo</option>
                @foreach($atributos as $atributo)
                    <option value="{{ $atributo->id }}">
                        {{ $atributo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-5">
            <label class="form-label">Valor</label>
            <select name="atributo_valor_ids[]" class="form-select valor-select">
                <option value="">Seleccione un valor</option>

                @foreach($atributos as $atributo)
                    @foreach($atributo->valores as $valor)
                        <option value="{{ $valor->id }}"
                                data-atributo="{{ $atributo->id }}"
                                style="display:none;">
                            {{ $valor->valor }}
                        </option>
                    @endforeach
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">
                <i class="bi bi-trash3"></i>
            </button>
        </div>
    </div>
</template>

<script>
    function actualizarValores(selectAtributo) {
        const fila = selectAtributo.closest('.fila-atributo');
        const atributoId = selectAtributo.value;
        const selectValor = fila.querySelector('.valor-select');

        selectValor.value = '';

        Array.from(selectValor.options).forEach(option => {
            if (!option.value) {
                option.style.display = '';
                return;
            }

            option.style.display = option.dataset.atributo === atributoId ? '' : 'none';
        });
    }

    function agregarFilaAtributo() {
        const template = document.getElementById('templateFilaAtributo');
        const contenedor = document.getElementById('contenedorAtributos');
        const clone = template.content.cloneNode(true);

        contenedor.appendChild(clone);
    }

    function eliminarFila(button) {
        const filas = document.querySelectorAll('.fila-atributo');

        if (filas.length <= 1) {
            alert('Debe quedar al menos un atributo para la variante.');
            return;
        }

        button.closest('.fila-atributo').remove();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.atributo-select').forEach(select => {
            if (select.value) {
                const fila = select.closest('.fila-atributo');
                const selectValor = fila.querySelector('.valor-select');
                const valorActual = selectValor.value;

                actualizarValores(select);

                selectValor.value = valorActual;
            }
        });
    });
</script>
@endsection