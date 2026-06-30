/* =====================================================
   Inventario — JS del módulo
   ===================================================== */

const InventarioModule = (function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const baseUrl   = document.getElementById('tablaContainer')?.dataset.url;

    let buscarTimeout = null;
    let almacenActual = new URLSearchParams(window.location.search).get('almacen') ?? '';
    let nivelActual   = new URLSearchParams(window.location.search).get('nivel')   ?? '';
    let buscarActual  = new URLSearchParams(window.location.search).get('buscar')  ?? '';

    function cargarTabla(params = {}) {
        const container = document.getElementById('tablaContainer');
        if (!container || !baseUrl) return;

        container.classList.add('loading');

        const url = new URL(baseUrl);
        if (params.buscar)  url.searchParams.set('buscar',  params.buscar);
        if (params.almacen) url.searchParams.set('almacen', params.almacen);
        if (params.nivel)   url.searchParams.set('nivel',   params.nivel);
        if (params.page)    url.searchParams.set('page',    params.page);

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
                cargarTabla({ buscar: buscarActual, almacen: almacenActual, nivel: nivelActual, page });
            });
        });
    }

    function bindFiltros() {
        document.querySelectorAll('.em-filtro').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.em-filtro').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                nivelActual = this.dataset.nivel ?? '';
                cargarTabla({ buscar: buscarActual, almacen: almacenActual, nivel: nivelActual });
            });
        });

        document.getElementById('filtroAlmacen')?.addEventListener('change', function () {
            almacenActual = this.value;
            cargarTabla({ buscar: buscarActual, almacen: almacenActual, nivel: nivelActual });
        });
    }

    function bindBuscador() {
        const input = document.getElementById('buscadorStock');
        if (!input) return;
        input.addEventListener('input', function () {
            clearTimeout(buscarTimeout);
            buscarActual = this.value.trim();
            buscarTimeout = setTimeout(() => {
                cargarTabla({ buscar: buscarActual, almacen: almacenActual, nivel: nivelActual });
            }, 400);
        });
    }

    function init() {
        bindFiltros();
        bindBuscador();
        bindPaginacion();
    }

    return { init };

})();

document.addEventListener('DOMContentLoaded', () => InventarioModule.init());