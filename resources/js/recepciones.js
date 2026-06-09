/* =====================================================
   Recepciones — JS del módulo
   ===================================================== */

document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const baseUrl   = document.getElementById('tablaContainer')?.dataset.url;

    let buscarTimeout = null;
    let tipoActual    = new URLSearchParams(window.location.search).get('tipo')   ?? '';
    let buscarActual  = new URLSearchParams(window.location.search).get('buscar') ?? '';

    // ── Index AJAX ──────────────────────────────────
    function cargarTabla(params = {}) {
        const container = document.getElementById('tablaContainer');
        if (!container || !baseUrl) return;

        container.classList.add('loading');

        const url = new URL(baseUrl);
        if (params.buscar) url.searchParams.set('buscar', params.buscar);
        if (params.tipo)   url.searchParams.set('tipo',   params.tipo);
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
                cargarTabla({ buscar: buscarActual, tipo: tipoActual, page });
            });
        });
    }

    function bindFiltros() {
        document.querySelectorAll('.em-filtro').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.em-filtro').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                tipoActual = this.dataset.tipo ?? '';
                cargarTabla({ buscar: buscarActual, tipo: tipoActual });
            });
        });
    }

    function bindBuscador() {
        const input = document.getElementById('buscadorRecepciones');
        if (!input) return;
        input.addEventListener('input', function () {
            clearTimeout(buscarTimeout);
            buscarActual = this.value.trim();
            buscarTimeout = setTimeout(() => {
                cargarTabla({ buscar: buscarActual, tipo: tipoActual });
            }, 400);
        });
    }

    bindFiltros();
    bindBuscador();
    bindPaginacion();

    // ── Formulario create ───────────────────────────
    const tipoSelect    = document.getElementById('tipoRecepcion');
    const motivoWrapper = document.getElementById('motivoRechazoWrapper');

    function toggleMotivo() {
        if (!tipoSelect || !motivoWrapper) return;
        motivoWrapper.style.display =
            tipoSelect.value === 'no_conforme' ? 'block' : 'none';
    }

    tipoSelect?.addEventListener('change', toggleMotivo);
    toggleMotivo();

    // ── Tom Select para líneas por características ──
    document.querySelectorAll('[id^="varianteCaract-"]').forEach(el => {
        new TomSelect(el, {
            valueField:  'id',
            labelField:  'texto',
            searchField: ['texto', 'codigo'],
            placeholder: 'Buscar variante o dejar sin asociar...',
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
                    return `<div style="padding:10px 12px; font-size:13px;
                                        color:var(--text-muted);">
                        No se encontraron variantes.
                    </div>`;
                },
            },
        });
    });
});