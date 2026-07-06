/* =====================================================
   Descuentos — JS del módulo
   ===================================================== */

const DescuentosModule = (function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const baseUrl   = document.getElementById('tablaContainer')?.dataset.url;

    let buscarTimeout = null;
    let estadoActual  = new URLSearchParams(window.location.search).get('estado') ?? '';
    let buscarActual  = new URLSearchParams(window.location.search).get('buscar') ?? '';

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
        const input = document.getElementById('buscadorDescuentos');
        if (!input) return;
        input.addEventListener('input', function () {
            clearTimeout(buscarTimeout);
            buscarActual = this.value.trim();
            buscarTimeout = setTimeout(() => {
                cargarTabla({ buscar: buscarActual, estado: estadoActual });
            }, 400);
        });
    }

    // ── Toggle de tipo de aplicación ────────────────
    function initToggleAplicacion() {
        const botones = document.querySelectorAll('.tipo-aplicacion-btn');
        if (botones.length === 0) return;

        botones.forEach(btn => {
            btn.addEventListener('click', function () {
                const tipo = this.dataset.aplicaA;

                botones.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                document.getElementById('aplicaAInput').value = tipo;

                document.querySelectorAll('.campo-aplicacion').forEach(campo => {
                    campo.style.display = 'none';
                });

                const campoActivo = document.getElementById(`campo-${tipo}`);
                if (campoActivo) campoActivo.style.display = 'block';
            });
        });
    }

    // ── Inicializar Tom Select para variantes (múltiple) ──
    function initTomSelectVariantes() {
        const el = document.getElementById('selectVariantes');
        if (!el || typeof TomSelect === 'undefined') return;

        new TomSelect(el, {
            valueField:  'id',
            labelField:  'texto',
            searchField: ['texto', 'codigo'],
            placeholder: 'Buscar productos o variantes...',
            plugins: ['remove_button'],
            load(query, callback) {
                if (query.length < 2) return callback();
                fetch(`/api/variantes/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(r => r.json())
                .then(data => callback(data))
                .catch(() => callback());
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
                    return `<div style="padding:10px 12px; font-size:13px; color:var(--text-muted);">
                        No se encontraron variantes.
                    </div>`;
                },
            },
        });
    }

    function init() {
        bindFiltros();
        bindBuscador();
        bindPaginacion();
        initToggleAplicacion();
        initTomSelectVariantes();
    }

    return { init };

})();

document.addEventListener('DOMContentLoaded', () => DescuentosModule.init());