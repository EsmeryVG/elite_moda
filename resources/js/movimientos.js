/* =====================================================
   Movimientos de Inventario — JS del módulo
   ===================================================== */

const MovimientosModule = (function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const baseUrl   = document.getElementById('tablaContainer')?.dataset.url;

    let buscarTimeout  = null;
    let tipoActual     = new URLSearchParams(window.location.search).get('tipo')    ?? '';
    let almacenActual  = new URLSearchParams(window.location.search).get('almacen') ?? '';
    let buscarActual   = new URLSearchParams(window.location.search).get('buscar')  ?? '';

    function cargarTabla(params = {}) {
        const container = document.getElementById('tablaContainer');
        if (!container || !baseUrl) return;

        container.classList.add('loading');

        const url = new URL(baseUrl);
        if (params.buscar)  url.searchParams.set('buscar',  params.buscar);
        if (params.tipo)    url.searchParams.set('tipo',    params.tipo);
        if (params.almacen) url.searchParams.set('almacen', params.almacen);
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
                cargarTabla({ buscar: buscarActual, tipo: tipoActual, almacen: almacenActual, page });
            });
        });
    }

    function bindFiltros() {
        document.querySelectorAll('.em-filtro').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.em-filtro').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                tipoActual = this.dataset.tipo ?? '';
                cargarTabla({ buscar: buscarActual, tipo: tipoActual, almacen: almacenActual });
            });
        });

        document.getElementById('filtroAlmacen')?.addEventListener('change', function () {
            almacenActual = this.value;
            cargarTabla({ buscar: buscarActual, tipo: tipoActual, almacen: almacenActual });
        });
    }

    function bindBuscador() {
        const input = document.getElementById('buscadorMovimientos');
        if (!input) return;
        input.addEventListener('input', function () {
            clearTimeout(buscarTimeout);
            buscarActual = this.value.trim();
            buscarTimeout = setTimeout(() => {
                cargarTabla({ buscar: buscarActual, tipo: tipoActual, almacen: almacenActual });
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

document.addEventListener('DOMContentLoaded', () => MovimientosModule.init());