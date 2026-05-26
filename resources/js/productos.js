/* =====================================================
   Productos — JS del módulo
   ===================================================== */

const ProductosModule = (function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    let buscarTimeout   = null;
    let estadoActual    = new URLSearchParams(window.location.search).get('estado')    ?? '';
    let categoriaActual = new URLSearchParams(window.location.search).get('categoria') ?? '';
    let buscarActual    = new URLSearchParams(window.location.search).get('buscar')    ?? '';

    // ════════════════════════════════════════════════
    // ÍNDICE — AJAX
    // ════════════════════════════════════════════════

    function getBaseUrl() {
        return document.getElementById('tablaContainer')?.dataset.url;
    }

    function cargarTabla(params = {}) {
        const container = document.getElementById('tablaContainer');
        const baseUrl   = getBaseUrl();
        if (!container || !baseUrl) return;

        container.classList.add('loading');

        const url = new URL(baseUrl);
        if (params.buscar)    url.searchParams.set('buscar',    params.buscar);
        if (params.estado)    url.searchParams.set('estado',    params.estado);
        if (params.categoria) url.searchParams.set('categoria', params.categoria);
        if (params.page)      url.searchParams.set('page',      params.page);

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
            bindExpandirVariantes();
        })
        .catch(() => container.classList.remove('loading'));
    }

    function bindPaginacion() {
        document.querySelectorAll('.ajax-page').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const page = new URL(this.href).searchParams.get('page') ?? 1;
                cargarTabla({ buscar: buscarActual, estado: estadoActual, categoria: categoriaActual, page });
            });
        });
    }

    function bindFiltros() {
        document.querySelectorAll('.em-filtro').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.em-filtro').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                estadoActual = this.dataset.estado ?? '';
                cargarTabla({ buscar: buscarActual, estado: estadoActual, categoria: categoriaActual });
            });
        });

        document.getElementById('filtroCategoria')?.addEventListener('change', function () {
            categoriaActual = this.value;
            cargarTabla({ buscar: buscarActual, estado: estadoActual, categoria: categoriaActual });
        });
    }

    function bindBuscador() {
        document.getElementById('buscadorProductos')?.addEventListener('input', function () {
            clearTimeout(buscarTimeout);
            buscarActual = this.value.trim();
            buscarTimeout = setTimeout(() => {
                cargarTabla({ buscar: buscarActual, estado: estadoActual, categoria: categoriaActual });
            }, 400);
        });
    }

    function bindExpandirVariantes() {
        document.querySelectorAll('.producto-row').forEach(row => {
            row.addEventListener('click', function (e) {
                if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form')) return;
                const id  = this.dataset.productoId;
                const sub = document.getElementById(`variantes-${id}`);
                if (!sub) return;
                this.classList.toggle('open');
                sub.classList.toggle('open');
            });
        });
    }

    // ════════════════════════════════════════════════
    // CREAR NUEVO VALOR AJAX
    // ════════════════════════════════════════════════

    function bindNuevoValor(input) {
        if (!input) return;

        input.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();

            const texto      = this.value.trim();
            const atributoId = this.dataset.atributoId;
            if (!texto || !atributoId) return;

            fetch(`/atributos/${atributoId}/valores`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ valor: texto, orden: 0 }),
            })
            .then(res => res.json())
            .then(data => {
                if (!data.id) {
                    alert(data.message ?? 'Error al crear el valor.');
                    return;
                }

                const checksContainer = document.getElementById(`checks-${atributoId}`);
                const wrapper         = this.closest('.nuevo-valor-wrapper');

                const div = document.createElement('div');
                div.className = 'form-check';
                div.innerHTML = `
                    <input class="form-check-input" type="checkbox"
                           id="val_${data.id}" value="${data.id}"
                           data-valor="${data.valor}" checked>
                    <label class="form-check-label" for="val_${data.id}">
                        ${data.valor}
                    </label>
                `;

                checksContainer.insertBefore(div, wrapper);
                this.value = '';
                this.focus();
            })
            .catch(() => alert('Error de conexión al crear el valor.'));
        });
    }

    function bindTodosLosNuevosValores() {
        document.querySelectorAll('.input-nuevo-valor').forEach(input => {
            bindNuevoValor(input);
        });
    }

    // ════════════════════════════════════════════════
    // CREATE — grilla de variantes
    // ════════════════════════════════════════════════

    function initCreate() {
        if (!document.getElementById('formCrearProducto')) return;

        document.getElementById('btnSimple')?.addEventListener('click',    () => setTipo('simple'));
        document.getElementById('btnVariantes')?.addEventListener('click', () => setTipo('variantes'));

        function setTipo(tipo) {
            const btnSimple    = document.getElementById('btnSimple');
            const btnVariantes = document.getElementById('btnVariantes');
            const secSimple    = document.getElementById('seccionSimple');
            const secVariantes = document.getElementById('seccionVariantes');

            if (tipo === 'simple') {
                btnSimple.classList.add('active');
                btnVariantes.classList.remove('active');
                secSimple.style.display    = 'block';
                secVariantes.style.display = 'none';
            } else {
                btnVariantes.classList.add('active');
                btnSimple.classList.remove('active');
                secVariantes.style.display = 'block';
                secSimple.style.display    = 'none';
                initTomSelect();
            }
        }

        let tomSelectInstance = null;

        function initTomSelect() {
            if (tomSelectInstance) return;

            const el = document.getElementById('selectorAtributos');
            if (!el || typeof TomSelect === 'undefined') return;

            tomSelectInstance = new TomSelect(el, {
                plugins: ['remove_button'],
                placeholder: 'Busca o selecciona atributos...',
                create: function (input, callback) {
                    fetch('/atributos', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ nombre: input }),
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.id) {
                            alert(data.message ?? 'Error al crear el atributo.');
                            callback();
                            return;
                        }

                        // Crear bloque de valores para el nuevo atributo
                        const contenedor = document.getElementById('contenedorValores');
                        const bloque     = document.createElement('div');
                        bloque.className          = 'atributo-grupo';
                        bloque.id                 = `grupo-${data.id}`;
                        bloque.dataset.nombre     = data.nombre;
                        bloque.style.display      = 'block';
                        bloque.innerHTML = `
                            <div class="atributo-grupo-nombre">${data.nombre}</div>
                            <div class="atributo-checks" id="checks-${data.id}">
                                <div class="nuevo-valor-wrapper">
                                    <input type="text"
                                           class="form-control form-control-sm input-nuevo-valor"
                                           placeholder="+ Nuevo valor, Enter para agregar"
                                           data-atributo-id="${data.id}"
                                           style="max-width:260px;">
                                </div>
                            </div>
                        `;
                        contenedor.appendChild(bloque);
                        bindNuevoValor(bloque.querySelector('.input-nuevo-valor'));

                        callback({ value: data.id, text: data.nombre });
                    })
                    .catch(() => {
                        alert('Error de conexión.');
                        callback();
                    });
                },
                createFilter: function (input) {
                    return input.trim().length > 0;
                },
                onItemAdd(value) {
                    const grupo = document.getElementById(`grupo-${value}`);
                    if (grupo) grupo.style.display = 'block';
                },
                onItemRemove(value) {
                    const grupo = document.getElementById(`grupo-${value}`);
                    if (grupo) {
                        grupo.style.display = 'none';
                        grupo.querySelectorAll('.form-check-input').forEach(cb => {
                            cb.checked = false;
                        });
                    }
                },
                render: {
                    option_create(data, escape) {
                        return `<div class="create">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Crear <strong>${escape(data.input)}</strong>
                                </div>`;
                    },
                },
            });
        }

        document.getElementById('btnGenerarGrilla')?.addEventListener('click', generarGrilla);
        bindTodosLosNuevosValores();
    }

    // ════════════════════════════════════════════════
    // GRILLA DE COMBINACIONES
    // ════════════════════════════════════════════════

    function generarGrilla() {
        const atributosSeleccionados = [];

        document.querySelectorAll('.atributo-grupo').forEach(grupo => {
            if (grupo.style.display === 'none') return;

            const nombre  = grupo.dataset.nombre;
            const checked = [...grupo.querySelectorAll('.form-check-input:checked')];
            if (checked.length === 0) return;

            atributosSeleccionados.push({
                nombre,
                valores: checked.map(cb => ({
                    id:    cb.value,
                    texto: cb.dataset.valor ?? cb.closest('.form-check')?.querySelector('label')?.textContent.trim() ?? cb.value,
                })),
            });
        });

        if (atributosSeleccionados.length === 0) {
            alert('Selecciona al menos un atributo con valores.');
            return;
        }

        const combinaciones = atributosSeleccionados.reduce((acc, atributo) => {
            if (acc.length === 0) {
                return atributo.valores.map(v => [{ atributo: atributo.nombre, ...v }]);
            }
            return acc.flatMap(combo =>
                atributo.valores.map(v => [...combo, { atributo: atributo.nombre, ...v }])
            );
        }, []);

        renderGrilla(combinaciones);
    }

    function renderGrilla(combinaciones) {
        const contenedor = document.getElementById('contenedorGrilla');
        if (!contenedor) return;

        if (combinaciones.length === 0) {
            contenedor.innerHTML = '';
            return;
        }

        const atributos = [...new Set(combinaciones[0].map(v => v.atributo))];

        let html = `
            <div class="grilla-variantes mt-3">
                <div class="precio-unico-bar">
                    <span>Aplicar precio a todas:</span>
                    <input type="number" id="precioUnicoInput" class="form-control"
                           placeholder="0.00" step="0.01" min="0">
                    <button type="button" class="btn btn-secondary btn-sm"
                            onclick="ProductosModule.aplicarPrecio()">
                        Aplicar
                    </button>
                </div>
                <table>
                    <thead>
                        <tr>
                            ${atributos.map(a => `<th>${a}</th>`).join('')}
                            <th>Precio de venta *</th>
                            <th style="width:40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="grillaBody">
        `;

        combinaciones.forEach((combo, i) => {
            html += `<tr>`;
            combo.forEach(v => {
                html += `
                    <td>
                        ${v.texto}
                        <input type="hidden"
                               name="variantes[${i}][atributo_valor_ids][]"
                               value="${v.id}">
                    </td>`;
            });
            html += `
                <td>
                    <input type="number" name="variantes[${i}][precio_venta]"
                           class="form-control precio-variante"
                           placeholder="0.00" step="0.01" min="0" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="this.closest('tr').remove(); ProductosModule.reindexar()">
                        <i class="bi bi-x"></i>
                    </button>
                </td>
            `;
            html += `</tr>`;
        });

        html += `</tbody></table></div>`;
        contenedor.innerHTML = html;
    }

    function reindexar() {
        document.querySelectorAll('#grillaBody tr').forEach((tr, i) => {
            tr.querySelectorAll('[name]').forEach(input => {
                input.name = input.name.replace(/variantes\[\d+\]/, `variantes[${i}]`);
            });
        });
    }

    function aplicarPrecio() {
        const v = document.getElementById('precioUnicoInput')?.value;
        if (v) document.querySelectorAll('.precio-variante').forEach(i => i.value = v);
    }

    // ════════════════════════════════════════════════
    // EDIT — agregar variante inline
    // ════════════════════════════════════════════════

function initEdit() {
    if (!document.getElementById('btnMostrarAgregarVariante')) return;

    const btn         = document.getElementById('btnMostrarAgregarVariante');
    const wrapper     = document.getElementById('wrapperAgregarVariante');
    const btnCancelar = document.getElementById('btnCancelarAgregarVariante');
    let   tomEdit     = null;

    btn.addEventListener('click', () => {
        wrapper.style.display = 'block';
        btn.style.display     = 'none';
        wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Inicializar Tom Select del edit
        if (!tomEdit) {
            const el = document.getElementById('selectorAtributosEdit');
            if (el && typeof TomSelect !== 'undefined') {
                tomEdit = new TomSelect(el, {
                    plugins: ['remove_button'],
                    placeholder: 'Busca o selecciona atributos...',
                    create: false,
                    onItemAdd(value) {
                        const grupo = document.getElementById(`grupo-edit-${value}`);
                        if (grupo) grupo.style.display = 'block';
                    },
                    onItemRemove(value) {
                        const grupo = document.getElementById(`grupo-edit-${value}`);
                        if (grupo) {
                            grupo.style.display = 'none';
                            grupo.querySelectorAll('.form-check-input')
                                 .forEach(cb => cb.checked = false);
                        }
                    },
                });
            }
        }
    });

    btnCancelar?.addEventListener('click', () => {
        wrapper.style.display = 'none';
        btn.style.display     = 'inline-flex';
    });

    // Bind nuevo valor en formulario de agregar variante del edit
    document.querySelectorAll('.input-nuevo-valor-edit').forEach(input => {
        bindNuevoValor(input);
    });
}

    // ════════════════════════════════════════════════
    // TOGGLE NUEVA MARCA
    // ════════════════════════════════════════════════

    function initToggleMarca() {
        const select = document.getElementById('marcaSelect');
        const cont   = document.getElementById('nuevaMarcaWrapper');
        if (!select || !cont) return;

        function toggle() {
            cont.style.display = select.value === '__otra__' ? 'block' : 'none';
        }

        select.addEventListener('change', toggle);
        toggle();
    }

    // ════════════════════════════════════════════════
    // INIT
    // ════════════════════════════════════════════════

    function init() {
        bindFiltros();
        bindBuscador();
        bindPaginacion();
        bindExpandirVariantes();
        initCreate();
        initEdit();
        initToggleMarca();
    }

    return { init, aplicarPrecio, reindexar };

})();

window.ProductosModule = ProductosModule;
document.addEventListener('DOMContentLoaded', () => ProductosModule.init());