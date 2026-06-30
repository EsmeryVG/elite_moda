/* =====================================================
   Ajustes de Inventario — JS del módulo
   ===================================================== */

const AjustesModule = (function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const baseUrl   = document.getElementById('tablaContainer')?.dataset.url;

    let buscarTimeout = null;
    let estadoActual  = new URLSearchParams(window.location.search).get('estado') ?? '';
    let buscarActual  = new URLSearchParams(window.location.search).get('buscar') ?? '';
    let contadorLineas = 0;

    // ════════════════════════════════════════════════
    // ÍNDICE — AJAX
    // ════════════════════════════════════════════════

    function cargarTabla(params = {}) {
        const container = document.getElementById('tablaContainer');
        if (!container || !baseUrl) return;

        container.classList.add('loading');

        const url = new URL(baseUrl);
        if (params.buscar) url.searchParams.set('buscar', params.buscar);
        if (params.estado) url.searchParams.set('estado', params.estado);
        if (params.page)   url.searchParams.set('page',   params.page);

        window.history.pushState({}, '', url.toString());

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            }
        })
        .then(res => res.text())
        .then(html => {
            container.innerHTML = html;
            container.classList.remove('loading');
            bindPaginacion();
        })
        .catch(() => container.classList.remove('loading'));
    }

    function bindPaginacion() {
        document.querySelectorAll('.ajax-page').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const page = new URL(this.href).searchParams.get('page') ?? 1;
                cargarTabla({ buscar: buscarActual, estado: estadoActual, page });
            });
        });
    }

    function bindFiltros() {
        document.querySelectorAll('.em-filtro').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.em-filtro').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                estadoActual = this.dataset.estado ?? '';
                cargarTabla({ buscar: buscarActual, estado: estadoActual });
            });
        });
    }

    function bindBuscador() {
        const input = document.getElementById('buscadorAjustes');
        if (!input) return;
        input.addEventListener('input', function () {
            clearTimeout(buscarTimeout);
            buscarActual = this.value.trim();
            buscarTimeout = setTimeout(() => {
                cargarTabla({ buscar: buscarActual, estado: estadoActual });
            }, 400);
        });
    }

    // ════════════════════════════════════════════════
    // FORMULARIO — LÍNEAS DINÁMICAS
    // ════════════════════════════════════════════════

        function initFormulario() {
            if (!document.getElementById('contenedorLineas')) return;

            document.getElementById('btnAgregarLinea')?.addEventListener('click', () => {
                agregarLinea();
            });

            document.getElementById('almacenSelect')?.addEventListener('change', () => {
                // Al cambiar almacén, recalcular cantidad sistema de todas las líneas
                document.querySelectorAll('.linea-ajuste-card').forEach(card => {
                    const idx = card.dataset.idx;
                    const varianteId = card.dataset.varianteId;
                    if (varianteId) {
                        cargarCantidadSistema(idx, varianteId);
                    }
                });
            });

            document.querySelector('select[name="tipo"]')?.addEventListener('change', () => {
                document.querySelectorAll('.linea-ajuste-card').forEach(card => {
                    actualizarDiferencia(card.dataset.idx);
                });
            });
        }

    function agregarLinea() {
        const contenedor = document.getElementById('contenedorLineas');
        const almacenId   = document.getElementById('almacenSelect')?.value;
        if (!contenedor) return;

        const idx = contadorLineas++;

        const card = document.createElement('div');
        card.className = 'linea-ajuste-card';
        card.dataset.idx = idx;

        card.innerHTML = `
            <div class="mb-3">
                <label style="font-size:11px; font-weight:600; text-transform:uppercase;
                              letter-spacing:0.06em; color:var(--text-muted); margin-bottom:6px;
                              display:block;">
                    Producto / Variante *
                </label>
                <select id="selectVariante-${idx}"
                        name="lineas[${idx}][variante_id]"
                        placeholder="Buscar producto o variante...">
                </select>
            </div>

            <div class="linea-ajuste-body">
                <div>
                    <label style="font-size:11px; font-weight:600; text-transform:uppercase;
                                  letter-spacing:0.06em; color:var(--text-muted); margin-bottom:6px;
                                  display:block;">
                        Observación
                    </label>
                    <input type="text"
                           name="lineas[${idx}][observacion]"
                           class="form-control"
                           placeholder="Opcional">
                </div>
                <div>
                    <label style="font-size:11px; font-weight:600; text-transform:uppercase;
                                  letter-spacing:0.06em; color:var(--text-muted); margin-bottom:6px;
                                  display:block;">
                        Cant. sistema
                    </label>
                    <input type="text" class="form-control cantidad-sistema"
                           value="0" disabled>
                </div>
                <div>
                    <label style="font-size:11px; font-weight:600; text-transform:uppercase;
                                  letter-spacing:0.06em; color:var(--text-muted); margin-bottom:6px;
                                  display:block;">
                        Cant. real *
                    </label>
                    <input type="number"
                           name="lineas[${idx}][cantidad_real]"
                           class="form-control cantidad-real"
                           value="0" min="0" step="1"
                           oninput="AjustesModule.actualizarDiferencia(${idx})">
                </div>
                <div>
                    <label style="font-size:11px; font-weight:600; text-transform:uppercase;
                                  letter-spacing:0.06em; color:var(--text-muted); margin-bottom:6px;
                                  display:block;">
                        Diferencia
                    </label>
                    <div class="diferencia-valor diferencia-neutra"
                         style="font-size:16px; padding:8px 0;">
                        0
                    </div>
                </div>
            </div>

            <div class="text-end mt-2">
                <button type="button" class="btn-eliminar-linea"
                        onclick="AjustesModule.eliminarLinea(${idx})">
                    <i class="bi bi-trash3 me-1"></i> Eliminar
                </button>
            </div>
        `;

        contenedor.appendChild(card);
        initTomSelectVariante(idx, almacenId);
    }

    function initTomSelectVariante(idx, almacenId) {
        const el = document.getElementById(`selectVariante-${idx}`);
        if (!el || typeof TomSelect === 'undefined') return;

        const ts = new TomSelect(el, {
            valueField:  'id',
            labelField:  'texto',
            searchField: ['texto', 'codigo'],
            placeholder: 'Buscar producto o variante...',
            load(query, callback) {
                if (query.length < 2) return callback();
                fetch(`/api/variantes/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(r => r.json())
                .then(data => callback(data))
                .catch(() => callback());
            },
            onChange(value) {
                const card = document.querySelector(`.linea-ajuste-card[data-idx="${idx}"]`);
                if (card) card.dataset.varianteId = value;
                cargarCantidadSistema(idx, value);
            },
            render: {
                option(data, escape) {
                    return `<div style="padding:8px 12px;">
                        <div style="font-size:13px; font-weight:500;">
                            ${escape(data.texto)}
                        </div>
                        <div style="font-size:11px; color:var(--text-muted);">
                            ${escape(data.codigo)}
                        </div>
                    </div>`;
                },
                no_results() {
                    return `<div style="padding:10px 12px; font-size:13px;
                                        color:var(--text-muted);">
                        No se encontraron variantes.
                    </div>`;
                },
            },
        });
    }

    function cargarCantidadSistema(idx, varianteId) {
        const almacenId = document.getElementById('almacenSelect')?.value;
        if (!varianteId || !almacenId) return;

        fetch(`/api/stock/buscar?variante_id=${varianteId}&almacen_id=${almacenId}`, {
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            const card = document.querySelector(`.linea-ajuste-card[data-idx="${idx}"]`);
            const input = card?.querySelector('.cantidad-sistema');
            if (input) {
                input.value = data.cantidad_sistema ?? 0;
                actualizarDiferencia(idx);
            }
        });
    }

        function actualizarDiferencia(idx) {
            const card = document.querySelector(`.linea-ajuste-card[data-idx="${idx}"]`);
            if (!card) return;

            const sistema = parseInt(card.querySelector('.cantidad-sistema')?.value) || 0;
            const real    = parseInt(card.querySelector('.cantidad-real')?.value)    || 0;
            const diferencia = real - sistema;
            const tipoAjuste = document.querySelector('select[name="tipo"]')?.value;

            const span = card.querySelector('.diferencia-valor');
            if (span) {
                span.textContent = diferencia > 0 ? `+${diferencia}` : diferencia;
                span.classList.remove('diferencia-positiva', 'diferencia-negativa', 'diferencia-neutra');
                span.classList.add(
                    diferencia > 0 ? 'diferencia-positiva' :
                    diferencia < 0 ? 'diferencia-negativa' : 'diferencia-neutra'
                );
            }

            // Advertencia visual si es merma/daño con diferencia positiva
            const inputReal = card.querySelector('.cantidad-real');
            const esMermaODaño = ['merma', 'daño'].includes(tipoAjuste);

            if (esMermaODaño && diferencia > 0) {
                inputReal.classList.add('is-invalid');
                let advertencia = card.querySelector('.advertencia-merma');
                if (!advertencia) {
                    advertencia = document.createElement('div');
                    advertencia.className = 'advertencia-merma text-danger mt-1';
                    advertencia.style.fontSize = '11px';
                    advertencia.style.gridColumn = '1 / -1';
                    inputReal.closest('div').appendChild(advertencia);
                }
                advertencia.textContent = 'No se permite aumentar stock en mermas o daños.';
            } else {
                inputReal.classList.remove('is-invalid');
                card.querySelector('.advertencia-merma')?.remove();
            }
        }

    function eliminarLinea(idx) {
        document.querySelector(`.linea-ajuste-card[data-idx="${idx}"]`)?.remove();
    }

    // ════════════════════════════════════════════════
    // INIT
    // ════════════════════════════════════════════════

    function init() {
        bindFiltros();
        bindBuscador();
        bindPaginacion();
        initFormulario();
    }

    return {
        init,
        agregarLinea,
        eliminarLinea,
        actualizarDiferencia,
    };

})();

window.AjustesModule = AjustesModule;
document.addEventListener('DOMContentLoaded', () => AjustesModule.init());