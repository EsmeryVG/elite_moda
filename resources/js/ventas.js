/* =====================================================
   Ventas / TPV — JS del módulo
   ===================================================== */

const VentasModule = (function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // ════════════════════════════════════════════════
    // ÍNDICE — AJAX (igual patrón que los demás módulos)
    // ════════════════════════════════════════════════

    function initIndex() {
        const baseUrl = document.getElementById('tablaContainer')?.dataset.url;
        if (!baseUrl) return;

        let buscarTimeout = null;
        let estadoActual  = new URLSearchParams(window.location.search).get('estado') ?? '';
        let buscarActual  = new URLSearchParams(window.location.search).get('buscar') ?? '';

        function cargarTabla(params = {}) {
            const container = document.getElementById('tablaContainer');
            container.classList.add('loading');

            const url = new URL(baseUrl);
            if (params.buscar) url.searchParams.set('buscar', params.buscar);
            if (params.estado) url.searchParams.set('estado', params.estado);
            if (params.page)   url.searchParams.set('page',   params.page);

            window.history.pushState({}, '', url.toString());

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
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

        document.querySelectorAll('.em-filtro').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.em-filtro').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                estadoActual = this.dataset.estado ?? '';
                cargarTabla({ buscar: buscarActual, estado: estadoActual });
            });
        });

        document.getElementById('buscadorVentas')?.addEventListener('input', function () {
            clearTimeout(buscarTimeout);
            buscarActual = this.value.trim();
            buscarTimeout = setTimeout(() => cargarTabla({ buscar: buscarActual, estado: estadoActual }), 400);
        });

        bindPaginacion();
    }

    // ════════════════════════════════════════════════
    // TPV — ESTADO DEL CARRITO
    // ════════════════════════════════════════════════

    let carrito             = [];
    let clienteActual       = null;
    let itbisPorcentaje     = 18;
    let itbisGlobalActivado = true;

    function initTPV() {
        const root = document.getElementById('tpvRoot');
        if (!root) return;

        itbisPorcentaje = parseFloat(root.dataset.itbis ?? 18);

        initBuscadorProductos();
        initBuscadorCliente();
        initBuscadorEmpleado();
        initToggleItbisGlobal();
        bindAgregarPago();
        bindFormSubmit();
        renderCarrito();
    }

    // ── Toggle global de ITBIS ──────────────────────
    function initToggleItbisGlobal() {
        const toggle = document.getElementById('itbisGlobalToggle');
        if (!toggle) return;

        toggle.addEventListener('change', function () {
            itbisGlobalActivado = this.checked;
            actualizarTotales();
        });
    }

    // ── Buscador de productos / escaneo código de barras ──
    function initBuscadorProductos() {
        const el = document.getElementById('buscadorProducto');
        if (!el || typeof TomSelect === 'undefined') return;

        const ts = new TomSelect(el, {
            valueField:  'id',
            labelField:  'texto',
            searchField: ['texto', 'codigo'],
            placeholder: 'Escanea un código de barras o busca por nombre...',
            maxOptions: 20,
            load(query, callback) {
                if (query.length < 1) return callback();
                fetch(`/api/productos/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(r => r.json())
                .then(data => callback(data))
                .catch(() => callback());
            },
            onChange(value) {
                if (!value) return;
                const item = ts.options[value];
                if (item) {
                    agregarAlCarrito(item);
                }
                ts.clear();
                ts.clearOptions();
                ts.focus();
            },
            render: {
                option(data, escape) {
                    const agotado = data.disponible <= 0;
                    return `<div style="padding:10px 14px; ${agotado ? 'opacity:0.5;' : ''}">
                        <div style="font-size:13.5px; font-weight:500;">
                            ${escape(data.texto)}
                        </div>
                        <div style="font-size:11.5px; color:var(--text-muted); display:flex; justify-content:space-between;">
                            <span>${escape(data.codigo)}</span>
                            <span>RD$ ${parseFloat(data.precio).toFixed(2)} ·
                                  ${agotado ? 'Agotado' : data.disponible + ' disp.'}</span>
                        </div>
                    </div>`;
                },
                no_results() {
                    return `<div style="padding:10px 14px; font-size:13px; color:var(--text-muted);">
                        No se encontraron productos.
                    </div>`;
                },
            },
        });

        // Permite escanear código de barras (Enter directo)
        el.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const val = ts.input.value;
                if (val.length > 5) {
                    fetch(`/api/productos/buscar?q=${encodeURIComponent(val)}`, {
                        headers: { 'X-CSRF-TOKEN': csrfToken }
                    })
                    .then(r => r.json())
                    .then(data => {
                        const match = data.find(d => d.codigo === val);
                        if (match) {
                            agregarAlCarrito(match);
                            ts.clear();
                            ts.clearOptions();
                        }
                    });
                }
            }
        });
    }

    function agregarAlCarrito(item) {
        if (item.disponible <= 0) {
            alert('Este producto está agotado.');
            return;
        }

        const existente = carrito.find(l => l.id === item.id);

        if (existente) {
            if (existente.cantidad + 1 > item.disponible) {
                alert(`Solo hay ${item.disponible} unidades disponibles.`);
                return;
            }
            existente.cantidad++;
        } else {
            carrito.push({
                id: item.id,
                texto: item.texto,
                codigo: item.codigo,
                precio: parseFloat(item.precio),
                disponible: item.disponible,
                cantidad: 1,
                descuentoUnitario: 0,
            });
            recalcularDescuento(carrito[carrito.length - 1]);
        }

        renderCarrito();
    }

    function recalcularDescuento(linea) {
        if (!clienteActual) {
            linea.descuentoUnitario = 0;
            return;
        }

        fetch(`/api/descuentos/calcular?variante_id=${linea.id}&cliente_id=${clienteActual.id}&precio=${linea.precio}`, {
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            linea.descuentoUnitario = parseFloat(data.monto ?? 0);
            renderCarrito();
        })
        .catch(() => {
            linea.descuentoUnitario = 0;
        });
    }

    function cambiarCantidad(idx, delta) {
        const linea = carrito[idx];
        if (!linea) return;

        const nuevaCantidad = linea.cantidad + delta;

        if (nuevaCantidad < 1) return;
        if (nuevaCantidad > linea.disponible) {
            alert(`Solo hay ${linea.disponible} unidades disponibles.`);
            return;
        }

        linea.cantidad = nuevaCantidad;
        renderCarrito();
    }

    function setCantidad(idx, valor) {
        const linea = carrito[idx];
        if (!linea) return;

        let cantidad = parseInt(valor) || 1;
        if (cantidad < 1) cantidad = 1;
        if (cantidad > linea.disponible) {
            cantidad = linea.disponible;
            alert(`Solo hay ${linea.disponible} unidades disponibles.`);
        }

        linea.cantidad = cantidad;
        renderCarrito();
    }

    function eliminarLinea(idx) {
        carrito.splice(idx, 1);
        renderCarrito();
    }

    // ── Buscador de cliente ─────────────────────────
    function initBuscadorCliente() {
        const el = document.getElementById('selectCliente');
        if (!el || typeof TomSelect === 'undefined') return;

        const ts = new TomSelect(el, {
            valueField: 'id',
            labelField: 'texto',
            searchField: ['texto'],
            placeholder: 'Buscar cliente por nombre o cédula...',
            load(query, callback) {
                if (query.length < 1) return callback();
                fetch(`/api/clientes/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(r => r.json())
                .then(data => callback(data))
                .catch(() => callback());
            },
            onChange(value) {
                if (!value) {
                    clienteActual = null;
                } else {
                    const item = ts.options[value];
                    clienteActual = { id: value, texto: item?.texto };
                }
                carrito.forEach(linea => recalcularDescuento(linea));
            },
        });

        // Preseleccionar Consumidor Final
        const defaultId = el.dataset.defaultId;
        const defaultTexto = el.dataset.defaultTexto;
        if (defaultId) {
            ts.addOption({ id: defaultId, texto: defaultTexto });
            ts.setValue(defaultId);
        }
    }

    // ── Buscador de empleado (vendedor) ─────────────
    function initBuscadorEmpleado() {
        const el = document.getElementById('selectEmpleado');
        if (!el || typeof TomSelect === 'undefined') return;

        new TomSelect(el, {
            valueField: 'id',
            labelField: 'texto',
            searchField: ['texto'],
            placeholder: 'Buscar vendedor por nombre o código...',
            load(query, callback) {
                if (query.length < 1) return callback();
                fetch(`/api/empleados/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(r => r.json())
                .then(data => callback(data))
                .catch(() => callback());
            },
        });
    }

    // ── Render del carrito ───────────────────────────
    function renderCarrito() {
        const contenedor = document.getElementById('carritoLista');
        if (!contenedor) return;

        if (carrito.length === 0) {
            contenedor.innerHTML = `
                <div class="carrito-vacio">
                    <i class="bi bi-cart3" style="font-size:36px; display:block; margin-bottom:10px;"></i>
                    Escanea o busca un producto para comenzar
                </div>`;
            actualizarTotales();
            sincronizarInputsOcultos();
            return;
        }

        contenedor.innerHTML = carrito.map((linea, idx) => {
            const subtotalLinea = (linea.precio - linea.descuentoUnitario) * linea.cantidad;
            const tieneDescuento = linea.descuentoUnitario > 0;

            return `
                <div class="carrito-item">
                    <div class="carrito-item-info">
                        <div class="carrito-item-nombre">${linea.texto}</div>
                        <div class="carrito-item-detalle">${linea.codigo}</div>
                        ${tieneDescuento ? `<div class="carrito-item-descuento">
                            <i class="bi bi-tag-fill me-1"></i>Descuento: -RD$ ${linea.descuentoUnitario.toFixed(2)} c/u
                        </div>` : ''}
                    </div>
                    <div class="carrito-item-cantidad">
                        <button type="button" class="carrito-cantidad-btn"
                                onclick="VentasModule.cambiarCantidad(${idx}, -1)">−</button>
                        <input type="number" class="carrito-cantidad-input" value="${linea.cantidad}"
                               onchange="VentasModule.setCantidad(${idx}, this.value)">
                        <button type="button" class="carrito-cantidad-btn"
                                onclick="VentasModule.cambiarCantidad(${idx}, 1)">+</button>
                    </div>
                    <div class="carrito-item-precio">
                        ${tieneDescuento ? `<span class="carrito-precio-original">RD$ ${(linea.precio * linea.cantidad).toFixed(2)}</span>` : ''}
                        RD$ ${subtotalLinea.toFixed(2)}
                    </div>
                    <button type="button" class="btn-eliminar-carrito"
                            onclick="VentasModule.eliminarLinea(${idx})">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            `;
        }).join('');

        actualizarTotales();
        sincronizarInputsOcultos();
    }

    function actualizarTotales() {
        let subtotalSinItbis = 0;
        let impuestoTotal    = 0;
        let descuentoTotal   = 0;

        carrito.forEach(linea => {
            const totalSinDescuento = linea.precio * linea.cantidad;
            const descuentoLinea    = linea.descuentoUnitario * linea.cantidad;
            const totalConDescuento = totalSinDescuento - descuentoLinea;

            descuentoTotal += descuentoLinea;

            if (itbisGlobalActivado) {
                const base  = totalConDescuento / (1 + itbisPorcentaje / 100);
                const itbis = totalConDescuento - base;
                subtotalSinItbis += base;
                impuestoTotal    += itbis;
            } else {
                subtotalSinItbis += totalConDescuento;
            }
        });

        const total = subtotalSinItbis + impuestoTotal;

        const fmt = v => 'RD$ ' + v.toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('resumenSubtotal').textContent  = fmt(subtotalSinItbis);
        document.getElementById('resumenDescuento').textContent = '-' + fmt(descuentoTotal);
        document.getElementById('resumenImpuesto').textContent  = fmt(impuestoTotal);
        document.getElementById('resumenTotal').textContent     = fmt(total);

        actualizarRestantePago(total);
    }

    function sincronizarInputsOcultos() {
        const contenedor = document.getElementById('lineasOcultas');
        if (!contenedor) return;

        contenedor.innerHTML = carrito.map((linea, idx) => `
            <input type="hidden" name="lineas[${idx}][variante_id]" value="${linea.id}">
            <input type="hidden" name="lineas[${idx}][cantidad]" value="${linea.cantidad}">
        `).join('');
    }

    // ── Pagos múltiples — tarjetas ───────────────────
    let contadorPagos = 0;

    const tiposPagoConReferencia = ['tarjeta', 'cheque', 'transferencia'];

    function bindAgregarPago() {
        document.getElementById('btnAgregarPago')?.addEventListener('click', () => agregarLineaPago());
        agregarLineaPago(); // primera línea por defecto
    }

    function agregarLineaPago() {
        const contenedor = document.getElementById('pagosContainer');
        if (!contenedor) return;

        const idx = contadorPagos++;
        const template = document.getElementById('tiposPagoTemplate');
        const tiposPagoSelect = template ? template.innerHTML : '';

        const div = document.createElement('div');
        div.className = 'pago-card';
        div.dataset.idx = idx;
        div.innerHTML = `
            <div class="pago-card-header">
                <select name="pagos[${idx}][tipo_pago_id]" class="pago-tipo-select"
                        onchange="VentasModule.onCambioTipoPago(${idx}, this)">
                    ${tiposPagoSelect}
                </select>
                <button type="button" class="btn-eliminar-pago" onclick="VentasModule.eliminarPago(${idx})">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="pago-monto-wrapper">
                <span class="pago-monto-prefix">RD$</span>
                <input type="number" name="pagos[${idx}][monto]" class="pago-monto-input pago-monto"
                       placeholder="0.00" min="0" step="0.01"
                       oninput="VentasModule.actualizarRestante()">
                ${idx === 0 ? `<button type="button" class="btn-pago-exacto" onclick="VentasModule.pagoExacto(${idx})">
                    <i class="bi bi-lightning-charge-fill"></i> Exacto
                </button>` : ''}
            </div>
            <div class="pago-referencia-row" id="pagoReferencia-${idx}" style="display:none;">
                <input type="text" name="pagos[${idx}][referencia]" placeholder="Nro. referencia">
                <input type="text" name="pagos[${idx}][banco]" placeholder="Banco">
            </div>
        `;
        contenedor.appendChild(div);
    }

    function onCambioTipoPago(idx, select) {
        const nombreTipo = select.options[select.selectedIndex]?.text.toLowerCase() ?? '';
        const refRow = document.getElementById(`pagoReferencia-${idx}`);
        if (!refRow) return;

        const necesitaRef = tiposPagoConReferencia.some(t => nombreTipo.includes(t));
        refRow.style.display = necesitaRef ? 'grid' : 'none';
    }

    function pagoExacto(idx) {
        const total = parseFloat(document.getElementById('resumenTotal')?.textContent.replace(/[^0-9.]/g, '')) || 0;
        const card = document.querySelector(`.pago-card[data-idx="${idx}"]`);
        const input = card?.querySelector('.pago-monto');
        if (input) {
            input.value = total.toFixed(2);
            actualizarRestanteGlobal();
        }
    }

    function eliminarPago(idx) {
        document.querySelector(`.pago-card[data-idx="${idx}"]`)?.remove();
        actualizarRestanteGlobal();
    }

    function actualizarRestanteGlobal() {
        const total = parseFloat(document.getElementById('resumenTotal')?.textContent.replace(/[^0-9.]/g, '')) || 0;
        actualizarRestantePago(total);
    }

    function actualizarRestantePago(total) {
        const montos = Array.from(document.querySelectorAll('.pago-monto'))
            .map(input => parseFloat(input.value) || 0);
        const pagado = montos.reduce((a, b) => a + b, 0);
        const restante = total - pagado;

        const el = document.getElementById('pagoRestante');
        if (!el) return;

        if (Math.abs(restante) < 0.01 && total > 0) {
            el.className = 'pago-restante completo';
            el.innerHTML = '✓ Pago completo';
        } else if (restante > 0) {
            el.className = 'pago-restante incompleto';
            el.innerHTML = `Faltan RD$ ${restante.toFixed(2)}`;
        } else {
            el.className = 'pago-restante completo';
            el.innerHTML = `
                <div class="cambio-box">
                    <div class="cambio-label">CAMBIO A ENTREGAR</div>
                    <div class="cambio-valor">RD$ ${Math.abs(restante).toFixed(2)}</div>
                </div>`;
        }
    }

    // ── Submit del formulario ────────────────────────
    function bindFormSubmit() {
        document.getElementById('formVenta')?.addEventListener('submit', function (e) {
            if (carrito.length === 0) {
                e.preventDefault();
                alert('Agrega al menos un producto al carrito.');
                return;
            }

            const total = parseFloat(document.getElementById('resumenTotal')?.textContent.replace(/[^0-9.]/g, '')) || 0;
            const montos = Array.from(document.querySelectorAll('.pago-monto'))
                .map(input => parseFloat(input.value) || 0);
            const pagado = montos.reduce((a, b) => a + b, 0);

            if (pagado < total - 0.01) {
                e.preventDefault();
                alert('El monto pagado no puede ser menor al total de la venta.');
            }
        });
    }

    function init() {
        initIndex();
        initTPV();
    }

    return {
        init,
        cambiarCantidad,
        setCantidad,
        eliminarLinea,
        eliminarPago,
        onCambioTipoPago,
        pagoExacto,
        actualizarRestante: actualizarRestanteGlobal,
    };

})();

window.VentasModule = VentasModule;
document.addEventListener('DOMContentLoaded', () => VentasModule.init());