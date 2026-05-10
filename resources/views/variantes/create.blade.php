@extends('layouts.app')

@section('page_title', 'Crear variante')
@section('page_subtitle', 'Selecciona atributos y valores para definir esta variante')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap');

    :root {
        --vt-bg:           #f5f4f1;
        --vt-surface:      #ffffff;
        --vt-border:       #e2e0d9;
        --vt-primary:      #2563eb;
        --vt-primary-soft: #eff4ff;
        --vt-danger:       #dc2626;
        --vt-danger-soft:  #fff1f1;
        --vt-text:         #18181b;
        --vt-muted:        #71717a;
        --vt-accent:       #f59e0b;
        --vt-radius:       12px;
        --vt-shadow:       0 2px 16px rgba(0,0,0,.07), 0 1px 3px rgba(0,0,0,.04);
    }

    /* ── wrapper ── */
    .vt-wrap {
        max-width: 760px;
        margin: 0 auto;
        padding: 1.5rem 1rem 3rem;
        font-family: 'DM Sans', system-ui, sans-serif;
    }

    /* ── page header ── */
    .vt-page-header {
        display: flex;
        align-items: center;
        gap: .9rem;
        margin-bottom: 1.6rem;
    }
    .vt-page-icon {
        width: 48px; height: 48px;
        background: var(--vt-primary);
        border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.35rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(37,99,235,.32);
    }
    .vt-page-title {
        font-family: 'Syne', system-ui, sans-serif;
        font-size: 1.55rem; font-weight: 700;
        color: var(--vt-text);
        margin: 0 0 .3rem;
        line-height: 1.1;
    }
    .vt-product-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        background: var(--vt-primary-soft);
        color: var(--vt-primary);
        font-size: .78rem; font-weight: 500;
        padding: .22rem .6rem;
        border-radius: 99px;
        border: 1px solid #c7d9ff;
        line-height: 1.5;
    }

    /* ── card ── */
    .vt-card {
        background: var(--vt-surface);
        border: 1px solid var(--vt-border);
        border-radius: 16px;
        box-shadow: var(--vt-shadow);
        padding: 1.75rem 2rem;
    }

    /* ── section title ── */
    .vt-section-title {
        font-size: .7rem;
        font-weight: 600;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--vt-muted);
        margin-bottom: .9rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    /* ── form labels ── */
    .vt-label {
        display: flex;
        align-items: center;
        gap: .35rem;
        font-size: .83rem;
        font-weight: 500;
        color: var(--vt-text);
        margin-bottom: .4rem;
    }
    .vt-label i { color: var(--vt-primary); font-size: .9rem; }

    /* ── inputs ── */
    .vt-input, .vt-textarea, .vt-select {
        font-family: 'DM Sans', system-ui, sans-serif;
        font-size: .9rem;
        color: var(--vt-text);
        background: #fafaf8;
        border: 1.5px solid var(--vt-border);
        border-radius: var(--vt-radius);
        padding: .6rem .85rem;
        width: 100%;
        transition: border-color .16s, box-shadow .16s, background .16s;
        outline: none;
        box-sizing: border-box;
    }
    .vt-textarea { resize: vertical; min-height: 85px; }
    .vt-input:focus, .vt-textarea:focus, .vt-select:focus {
        border-color: var(--vt-primary);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .vt-input::placeholder, .vt-textarea::placeholder { color: #b0aead; }

    /* prefix $ */
    .vt-input-wrap { position: relative; }
    .vt-prefix {
        position: absolute; left: .85rem; top: 50%;
        transform: translateY(-50%);
        color: var(--vt-muted); font-size: .88rem; pointer-events: none;
        font-weight: 500;
    }
    .vt-input-wrap .vt-input { padding-left: 1.7rem; }

    /* ── separator ── */
    .vt-sep { height: 1px; background: var(--vt-border); margin: 1.5rem 0; }

    /* ── attr section header ── */
    .vt-attr-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .85rem;
    }

    /* ── attribute row ── */
    .vt-attr-row {
        display: grid;
        grid-template-columns: 1fr 1fr 40px;
        gap: .65rem;
        align-items: end;
        background: #fafaf8;
        border: 1.5px solid var(--vt-border);
        border-radius: var(--vt-radius);
        padding: .9rem .9rem .75rem;
        margin-bottom: .6rem;
        transition: border-color .16s, box-shadow .16s, background .16s;
    }
    .vt-attr-row:focus-within {
        border-color: var(--vt-primary);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37,99,235,.07);
    }
    @keyframes vtRowIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .vt-attr-row { animation: vtRowIn .2s ease; }

    /* select custom arrow */
    .vt-sel-wrap { position: relative; }
    .vt-sel-wrap::after {
        content: '';
        position: absolute; right: .8rem; top: 50%;
        transform: translateY(-50%);
        width: 0; height: 0;
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 5px solid var(--vt-muted);
        pointer-events: none;
    }
    .vt-select {
        appearance: none;
        -webkit-appearance: none;
        padding-right: 2rem;
        cursor: pointer;
    }
    .vt-select:disabled {
        color: var(--vt-muted);
        background: #f2f1ef;
        cursor: default;
    }

    /* attr row number badge */
    .vt-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 20px; height: 20px;
        background: var(--vt-primary);
        color: #fff;
        border-radius: 5px;
        font-size: .68rem; font-weight: 700;
        margin-right: .3rem;
        flex-shrink: 0;
    }

    /* ── delete button ── */
    .vt-del {
        width: 40px; height: 40px;
        border: 1.5px solid #fca5a5;
        border-radius: 9px;
        background: var(--vt-danger-soft);
        color: var(--vt-danger);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: background .15s, transform .15s;
        flex-shrink: 0;
        font-size: 1rem;
    }
    .vt-del:hover { background: #fce8e8; transform: scale(1.07); }

    /* ── add row button ── */
    .vt-add-row {
        display: flex; align-items: center; justify-content: center; gap: .5rem;
        width: 100%;
        padding: .55rem;
        border: 1.5px dashed #a8b4c8;
        border-radius: var(--vt-radius);
        background: transparent;
        color: var(--vt-muted);
        font-family: 'DM Sans', sans-serif;
        font-size: .875rem; font-weight: 500;
        cursor: pointer;
        transition: border-color .15s, color .15s, background .15s;
        margin-top: .25rem;
    }
    .vt-add-row:hover {
        border-color: var(--vt-primary);
        color: var(--vt-primary);
        background: var(--vt-primary-soft);
    }

    /* ── tip box ── */
    .vt-tip {
        display: flex; gap: .65rem; align-items: flex-start;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 10px;
        padding: .75rem .9rem;
        font-size: .82rem;
        color: #92400e;
        margin-top: 1.2rem;
    }
    .vt-tip i { color: var(--vt-accent); flex-shrink: 0; margin-top: .1rem; }

    /* ── error box ── */
    .vt-errors {
        background: var(--vt-danger-soft);
        border: 1px solid #fca5a5;
        border-radius: 10px;
        padding: .75rem 1rem;
        margin-bottom: 1.25rem;
        font-size: .87rem;
        color: var(--vt-danger);
    }
    .vt-errors ul { margin: 0; padding-left: 1.2rem; }

    /* ── footer ── */
    .vt-footer {
        display: flex; justify-content: flex-end; gap: .65rem;
        padding-top: 1.25rem;
        margin-top: 1.5rem;
        border-top: 1px solid var(--vt-border);
    }
    .vt-btn {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .58rem 1.25rem;
        border-radius: 10px;
        font-family: 'DM Sans', sans-serif;
        font-size: .88rem; font-weight: 500;
        cursor: pointer;
        transition: all .16s;
        text-decoration: none;
        border: none;
        line-height: 1.4;
    }
    .vt-btn-ghost {
        background: #f5f4f1;
        border: 1.5px solid var(--vt-border);
        color: var(--vt-muted);
    }
    .vt-btn-ghost:hover { background: #ede9df; color: var(--vt-text); }
    .vt-btn-primary {
        background: var(--vt-primary);
        color: #fff;
        box-shadow: 0 3px 12px rgba(37,99,235,.28);
    }
    .vt-btn-primary:hover {
        background: #1d4ed8;
        box-shadow: 0 5px 16px rgba(37,99,235,.38);
        transform: translateY(-1px);
        color: #fff;
    }

    /* ── responsive ── */
    @media (max-width: 560px) {
        .vt-card { padding: 1.2rem 1rem; }
        .vt-attr-row { grid-template-columns: 1fr; }
        .vt-del { width: 100%; height: 36px; }
    }
</style>

<div class="vt-wrap">

    {{-- Page header --}}
    <div class="vt-page-header">
        <div class="vt-page-icon"><i class="bi bi-layers-half"></i></div>
        <div>
            <h1 class="vt-page-title">Nueva variante</h1>
            <span class="vt-product-badge">
                <i class="bi bi-box-seam"></i>
                {{ $producto->nombre }}
            </span>
        </div>
    </div>

    <div class="vt-card">

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="vt-errors">
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('variantes.store') }}" method="POST">
            @csrf
            <input type="hidden" name="producto_id" value="{{ $producto->id }}">

            {{-- Section: General info --}}
            <div class="vt-section-title">
                <i class="bi bi-info-circle"></i> Información general
            </div>

            <div style="margin-bottom:1rem;">
                <label class="vt-label" for="descripcion">
                    <i class="bi bi-text-left"></i> Descripción
                </label>
                <textarea name="descripcion" id="descripcion" class="vt-textarea"
                          placeholder="Ej: Camiseta azul talla S">{{ old('descripcion') }}</textarea>
            </div>

            <div style="margin-bottom:.5rem;">
                <label class="vt-label" for="precio_venta">
                    <i class="bi bi-tag"></i> Precio de venta
                </label>
                <div class="vt-input-wrap">
                    <span class="vt-prefix">$</span>
                    <input type="number" step="0.01" name="precio_venta" id="precio_venta"
                           class="vt-input" value="{{ old('precio_venta') }}" placeholder="0.00">
                </div>
            </div>

            <div class="vt-sep"></div>

            {{-- Section: Attributes --}}
            <div class="vt-attr-header">
                <div class="vt-section-title" style="margin-bottom:0;">
                    <i class="bi bi-sliders"></i> Atributos de la variante
                </div>
                <span style="font-size:.75rem; color:var(--vt-muted);">
                    <i class="bi bi-info-circle"></i> Selecciona atributo → valor
                </span>
            </div>

            <div id="contenedorAtributos">
                @php $oldValores = old('atributo_valor_ids', []); @endphp

                @if(count($oldValores) > 0)
                    @foreach($oldValores as $i => $valorSeleccionado)
                        @php
                            $valorActual = null; $atributoSeleccionado = null;
                            foreach ($atributos as $at) {
                                foreach ($at->valores as $v) {
                                    if ((int)$v->id === (int)$valorSeleccionado) {
                                        $valorActual = $v;
                                        $atributoSeleccionado = $at->id;
                                    }
                                }
                            }
                        @endphp
                        <div class="vt-attr-row">
                            <div>
                                <label class="vt-label">
                                    <span class="vt-num">{{ $i + 1 }}</span> Atributo
                                </label>
                                <div class="vt-sel-wrap">
                                    <select class="vt-select atributo-select" onchange="vtActualizar(this)">
                                        <option value="">— Seleccionar —</option>
                                        @foreach($atributos as $at)
                                            <option value="{{ $at->id }}" {{ $atributoSeleccionado == $at->id ? 'selected' : '' }}>
                                                {{ $at->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="vt-label">
                                    <i class="bi bi-arrow-return-right" style="color:var(--vt-muted)!important;"></i> Valor
                                </label>
                                <div class="vt-sel-wrap">
                                    <select name="atributo_valor_ids[]" class="vt-select valor-select">
                                        <option value="">— Seleccionar valor —</option>
                                        @foreach($atributos as $at)
                                            @foreach($at->valores as $v)
                                                <option value="{{ $v->id }}"
                                                        data-atributo="{{ $at->id }}"
                                                        {{ (int)$valorSeleccionado === (int)$v->id ? 'selected' : '' }}
                                                        style="{{ $atributoSeleccionado == $at->id ? '' : 'display:none;' }}">
                                                    {{ $v->valor }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <button type="button" class="vt-del" onclick="vtEliminar(this)" title="Eliminar">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="vt-attr-row">
                        <div>
                            <label class="vt-label">
                                <span class="vt-num">1</span> Atributo
                            </label>
                            <div class="vt-sel-wrap">
                                <select class="vt-select atributo-select" onchange="vtActualizar(this)">
                                    <option value="">— Seleccionar —</option>
                                    @foreach($atributos as $at)
                                        <option value="{{ $at->id }}">{{ $at->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="vt-label">
                                <i class="bi bi-arrow-return-right" style="color:var(--vt-muted)!important;"></i> Valor
                            </label>
                            <div class="vt-sel-wrap">
                                <select name="atributo_valor_ids[]" class="vt-select valor-select" disabled>
                                    <option value="">Primero elige un atributo</option>
                                    @foreach($atributos as $at)
                                        @foreach($at->valores as $v)
                                            <option value="{{ $v->id }}" data-atributo="{{ $at->id }}" style="display:none;">
                                                {{ $v->valor }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="vt-del" onclick="vtEliminar(this)" title="Eliminar">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <button type="button" class="vt-add-row" onclick="vtAgregar()">
                <i class="bi bi-plus-circle-dotted"></i>
                Agregar otro atributo
            </button>


            <div class="vt-footer">
                <a href="{{ route('productos.show', $producto) }}" class="vt-btn vt-btn-ghost">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <button type="submit" class="vt-btn vt-btn-primary">
                    <i class="bi bi-check-circle-fill"></i> Guardar variante
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Hidden template for new rows --}}
<template id="vtTemplate">
    <div class="vt-attr-row">
        <div>
            <label class="vt-label">
                <span class="vt-num">•</span> Atributo
            </label>
            <div class="vt-sel-wrap">
                <select class="vt-select atributo-select" onchange="vtActualizar(this)">
                    <option value="">— Seleccionar —</option>
                    @foreach($atributos as $at)
                        <option value="{{ $at->id }}">{{ $at->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="vt-label">
                <i class="bi bi-arrow-return-right" style="color:var(--vt-muted)!important;"></i> Valor
            </label>
            <div class="vt-sel-wrap">
                <select name="atributo_valor_ids[]" class="vt-select valor-select" disabled>
                    <option value="">Primero elige un atributo</option>
                    @foreach($atributos as $at)
                        @foreach($at->valores as $v)
                            <option value="{{ $v->id }}" data-atributo="{{ $at->id }}" style="display:none;">
                                {{ $v->valor }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <button type="button" class="vt-del" onclick="vtEliminar(this)" title="Eliminar">
                <i class="bi bi-trash3"></i>
            </button>
        </div>
    </div>
</template>

<script>
    function vtActualizar(sel) {
        const fila = sel.closest('.vt-attr-row');
        const atributoId = sel.value;
        const selValor = fila.querySelector('.valor-select');

        selValor.value = '';
        Array.from(selValor.options).forEach(opt => {
            if (!opt.value) return;
            opt.style.display = opt.dataset.atributo === atributoId ? '' : 'none';
        });

        if (atributoId) {
            selValor.disabled = false;
            selValor.options[0].textContent = '— Seleccionar valor —';
        } else {
            selValor.disabled = true;
            selValor.options[0].textContent = 'Primero elige un atributo';
        }
    }

    function vtRenumerar() {
        document.querySelectorAll('#contenedorAtributos .vt-attr-row').forEach((row, i) => {
            const badge = row.querySelector('.vt-num');
            if (badge) badge.textContent = i + 1;
        });
    }

    function vtAgregar() {
        const tpl = document.getElementById('vtTemplate');
        const clone = tpl.content.cloneNode(true);
        document.getElementById('contenedorAtributos').appendChild(clone);
        vtRenumerar();
    }

    function vtEliminar(btn) {
        const filas = document.querySelectorAll('#contenedorAtributos .vt-attr-row');
        if (filas.length <= 1) {
            const row = btn.closest('.vt-attr-row');
            row.style.borderColor = 'var(--vt-danger)';
            row.style.boxShadow   = '0 0 0 3px rgba(220,38,38,.1)';
            setTimeout(() => { row.style.borderColor = ''; row.style.boxShadow = ''; }, 800);
            return;
        }
        const row = btn.closest('.vt-attr-row');
        row.style.transition = 'opacity .18s, transform .18s';
        row.style.opacity = '0';
        row.style.transform = 'translateY(-5px)';
        setTimeout(() => { row.remove(); vtRenumerar(); }, 180);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.atributo-select').forEach(s => {
            if (s.value) vtActualizar(s);
        });
        vtRenumerar();
    });
</script>
@endsection