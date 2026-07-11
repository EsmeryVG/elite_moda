/* =====================================================
   Ventas / TPV — JS del módulo
   ===================================================== */

const VentasModule = (function () {
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;

    // ════════════════════════════════════════════════
    // ÍNDICE — AJAX
    // ════════════════════════════════════════════════

    function initIndex() {
        const baseUrl = document.getElementById("tablaContainer")?.dataset.url;
        if (!baseUrl) return;

        let buscarTimeout = null;
        let estadoActual =
            new URLSearchParams(window.location.search).get("estado") ?? "";
        let buscarActual =
            new URLSearchParams(window.location.search).get("buscar") ?? "";

        function cargarTabla(params = {}) {
            const container = document.getElementById("tablaContainer");
            container.classList.add("loading");

            const url = new URL(baseUrl);
            if (params.buscar) url.searchParams.set("buscar", params.buscar);
            if (params.estado) url.searchParams.set("estado", params.estado);
            if (params.page) url.searchParams.set("page", params.page);

            window.history.pushState({}, "", url.toString());

            fetch(url.toString(), {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrfToken,
                },
            })
                .then((res) => res.text())
                .then((html) => {
                    container.innerHTML = html;
                    container.classList.remove("loading");
                    bindPaginacion();
                })
                .catch(() => container.classList.remove("loading"));
        }

        function bindPaginacion() {
            document.querySelectorAll(".ajax-page").forEach((link) => {
                link.addEventListener("click", function (e) {
                    e.preventDefault();
                    const page =
                        new URL(this.href).searchParams.get("page") ?? 1;
                    cargarTabla({
                        buscar: buscarActual,
                        estado: estadoActual,
                        page,
                    });
                });
            });
        }

        document.querySelectorAll(".em-filtro").forEach((btn) => {
            btn.addEventListener("click", function () {
                document
                    .querySelectorAll(".em-filtro")
                    .forEach((b) => b.classList.remove("active"));
                this.classList.add("active");
                estadoActual = this.dataset.estado ?? "";
                cargarTabla({ buscar: buscarActual, estado: estadoActual });
            });
        });

        document
            .getElementById("buscadorVentas")
            ?.addEventListener("input", function () {
                clearTimeout(buscarTimeout);
                buscarActual = this.value.trim();
                buscarTimeout = setTimeout(
                    () =>
                        cargarTabla({
                            buscar: buscarActual,
                            estado: estadoActual,
                        }),
                    400,
                );
            });

        bindPaginacion();
    }

    // ════════════════════════════════════════════════
    // TPV — ESTADO DEL CARRITO
    // ════════════════════════════════════════════════

    let carrito = [];
    let clienteActual = null;
    let itbisPorcentaje = 18;
    let itbisGlobalActivado = true;
    let tiposPagoDisponibles = [];

    function initTPV() {
        const root = document.getElementById("tpvRoot");
        if (!root) return;

        itbisPorcentaje = parseFloat(root.dataset.itbis ?? 18);

        // Cargar tipos de pago del template — ordenados por prioridad de uso
        const template = document.getElementById("tiposPagoTemplate");
        if (template) {
            const opciones = template.content.querySelectorAll("option");
            const orden = ["efectivo", "tarjeta", "transferencia", "cheque"];
            tiposPagoDisponibles = Array.from(opciones)
                .map((o) => ({ id: o.value, nombre: o.textContent.trim() }))
                .filter((t) => t.nombre.toLowerCase() !== "crédito")
                .sort((a, b) => {
                    const idxA = orden.findIndex((o) =>
                        a.nombre.toLowerCase().includes(o),
                    );
                    const idxB = orden.findIndex((o) =>
                        b.nombre.toLowerCase().includes(o),
                    );
                    return (
                        (idxA === -1 ? 99 : idxA) - (idxB === -1 ? 99 : idxB)
                    );
                });
        }

        initBuscadorProductos();
        initBuscadorCliente();
        initBuscadorEmpleado();
        initToggleItbisGlobal();
        initCategorias();
        bindBtnCobrar();
        bindFormSubmit();
        renderCarrito();
    }

    // ── Toggle global de ITBIS ──────────────────────
    function initToggleItbisGlobal() {
        const toggle = document.getElementById("itbisGlobalToggle");
        if (!toggle) return;
        toggle.addEventListener("change", function () {
            itbisGlobalActivado = this.checked;
            actualizarTotales();
        });
    }

    // ── Buscador de productos ───────────────────────
    function initBuscadorProductos() {
        const el = document.getElementById("buscadorProducto");
        if (!el || typeof TomSelect === "undefined") return;

        const ts = new TomSelect(el, {
            valueField: "id",
            labelField: "texto",
            searchField: ["texto", "codigo"],
            placeholder: "Escanea un código de barras o busca por nombre...",
            maxOptions: 20,
            load(query, callback) {
                if (query.length < 1) return callback();
                fetch(`/api/productos/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                    .then((r) => r.json())
                    .then((data) => callback(data))
                    .catch(() => callback());
            },
            onChange(value) {
                if (!value) return;
                const item = ts.options[value];
                if (item) agregarAlCarrito(item);
                ts.clear();
                ts.clearOptions();
                ts.focus();
            },
            render: {
                option(data, escape) {
                    const agotado = data.disponible <= 0;
                    return `<div style="padding:10px 14px; ${agotado ? "opacity:0.5;" : ""}">
                        <div style="font-size:13.5px; font-weight:500;">${escape(data.texto)}</div>
                        <div style="font-size:11.5px; color:var(--text-muted); display:flex; justify-content:space-between;">
                            <span>${escape(data.codigo)}</span>
                            <span>RD$ ${parseFloat(data.precio).toFixed(2)} · ${agotado ? "Agotado" : data.disponible + " disp."}</span>
                        </div>
                    </div>`;
                },
                no_results() {
                    return `<div style="padding:10px 14px; font-size:13px; color:var(--text-muted);">No se encontraron productos.</div>`;
                },
            },
        });

        el.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                const val = ts.input.value;
                if (val.length > 5) {
                    fetch(
                        `/api/productos/buscar?q=${encodeURIComponent(val)}`,
                        {
                            headers: { "X-CSRF-TOKEN": csrfToken },
                        },
                    )
                        .then((r) => r.json())
                        .then((data) => {
                            const match = data.find((d) => d.codigo === val);
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
            alert("Este producto está agotado.");
            return;
        }

        const existente = carrito.find((l) => l.id === item.id);

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
                descuentoNombre: null,
            });
            recalcularDescuento(carrito[carrito.length - 1]);
        }

        renderCarrito();
    }

    function recalcularDescuento(linea) {
        if (!clienteActual) {
            linea.descuentoUnitario = 0;
            linea.descuentoNombre = null;
            return;
        }

        fetch(
            `/api/descuentos/calcular?variante_id=${linea.id}&cliente_id=${clienteActual.id}&precio=${linea.precio}`,
            {
                headers: { "X-CSRF-TOKEN": csrfToken },
            },
        )
            .then((r) => r.json())
            .then((data) => {
                linea.descuentoUnitario = parseFloat(data.monto ?? 0);
                linea.descuentoNombre = data.nombre ?? null;
                renderCarrito();
            })
            .catch(() => {
                linea.descuentoUnitario = 0;
                linea.descuentoNombre = null;
            });
    }

    function cambiarCantidad(idx, delta) {
        const linea = carrito[idx];
        if (!linea) return;
        const nueva = linea.cantidad + delta;
        if (nueva < 1) return;
        if (nueva > linea.disponible) {
            alert(`Solo hay ${linea.disponible} unidades disponibles.`);
            return;
        }
        linea.cantidad = nueva;
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
        const el = document.getElementById("selectCliente");
        if (!el || typeof TomSelect === "undefined") return;

        const ts = new TomSelect(el, {
            valueField: "id",
            labelField: "texto",
            searchField: ["texto"],
            placeholder: "Buscar cliente...",
            load(query, callback) {
                if (query.length < 1) return callback();
                fetch(`/api/clientes/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                    .then((r) => r.json())
                    .then((data) => callback(data))
                    .catch(() => callback());
            },
            onChange(value) {
                // Resetear crédito por defecto
                clienteActual = value
                    ? {
                          id: value,
                          texto: ts.options[value]?.texto,
                          tieneCredito: false,
                          creditoDisponible: 0,
                          limiteCredito: 0,
                          balanceCredito: 0,
                      }
                    : null;

                carrito.forEach((linea) => recalcularDescuento(linea));

                // Verificar crédito solo si hay cliente seleccionado
                if (value) {
                    fetch(`/api/clientes/credito?cliente_id=${value}`, {
                        headers: { "X-CSRF-TOKEN": csrfToken },
                    })
                        .then((r) => r.json())
                        .then((data) => {
                            if (clienteActual && clienteActual.id === value) {
                                clienteActual.tieneCredito =
                                    data.tiene_credito ?? false;
                                clienteActual.creditoDisponible = parseFloat(
                                    data.credito_disponible ?? 0,
                                );
                                clienteActual.limiteCredito = parseFloat(
                                    data.limite_credito ?? 0,
                                );
                                clienteActual.balanceCredito = parseFloat(
                                    data.balance_credito ?? 0,
                                );
                            }
                        });
                }
            },
        });

        const defaultId = el.dataset.defaultId;
        const defaultTexto = el.dataset.defaultTexto;
        if (defaultId) {
            ts.addOption({ id: defaultId, texto: defaultTexto });
            ts.setValue(defaultId);
        }
    }

    // ── Buscador de empleado ────────────────────────
    function initBuscadorEmpleado() {
        const el = document.getElementById("selectEmpleado");
        if (!el || typeof TomSelect === "undefined") return;

        new TomSelect(el, {
            valueField: "id",
            labelField: "texto",
            searchField: ["texto"],
            placeholder: "Buscar vendedor...",
            load(query, callback) {
                if (query.length < 1) return callback();
                fetch(`/api/empleados/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                    .then((r) => r.json())
                    .then((data) => callback(data))
                    .catch(() => callback());
            },
        });
    }

    // ── Render del carrito ───────────────────────────
    function renderCarrito() {
        const contenedor = document.getElementById("carritoLista");
        if (!contenedor) return;

        if (carrito.length === 0) {
            contenedor.innerHTML = `
                <div class="carrito-vacio">
                    <i class="bi bi-cart3" style="font-size:36px; display:block; margin-bottom:10px;"></i>
                    Escanea o busca un producto para comenzar
                </div>`;
            actualizarTotales();
            sincronizarInputsOcultos();

            // Forzar actualización a 0 del contador cuando se vacíe el carrito
            const countEl = document.getElementById("carritoCount");
            if (countEl) countEl.style.display = "none";
            return;
        }

        contenedor.innerHTML = carrito
            .map((linea, idx) => {
                const subtotalLinea =
                    (linea.precio - linea.descuentoUnitario) * linea.cantidad;
                const tieneDescuento = linea.descuentoUnitario > 0;
                return `
                <div class="carrito-item">
                   <div class="carrito-item-info">
    <div class="carrito-item-nombre">${linea.texto}</div>
    <div class="carrito-item-detalle">${linea.codigo}</div>
    ${
        tieneDescuento
            ? `
        <div class="carrito-item-descuento">
            <i class="bi bi-tag-fill me-1"></i>
            ${linea.descuentoNombre ? `<span>${linea.descuentoNombre}</span> · ` : ""}
            -RD$ ${linea.descuentoUnitario.toFixed(2)} c/u
        </div>`
            : ""
    }
</div>
                    <div class="carrito-item-cantidad">
                        <button type="button" class="carrito-cantidad-btn" onclick="VentasModule.cambiarCantidad(${idx}, -1)">−</button>
                        <input type="number" class="carrito-cantidad-input" value="${linea.cantidad}" onchange="VentasModule.setCantidad(${idx}, this.value)">
                        <button type="button" class="carrito-cantidad-btn" onclick="VentasModule.cambiarCantidad(${idx}, 1)">+</button>
                    </div>
                    <div class="carrito-item-precio">
                        RD$ ${subtotalLinea.toFixed(2)}
                    </div>
                    <button type="button" class="btn-eliminar-carrito" onclick="VentasModule.eliminarLinea(${idx})">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>`;
            })
            .join("");

        actualizarTotales();
        sincronizarInputsOcultos();

        // Actualizar contador del tab carrito
        const count = carrito.reduce((sum, l) => sum + l.cantidad, 0);
        const countEl = document.getElementById("carritoCount");
        if (countEl) {
            if (count > 0) {
                countEl.style.display = "inline";
                countEl.textContent = count;
            } else {
                countEl.style.display = "none";
            }
        }
    }

    function actualizarTotales() {
        let subtotalSinItbis = 0;
        let impuestoTotal = 0;
        let descuentoTotal = 0;

        carrito.forEach((linea) => {
            const totalSinDescuento = linea.precio * linea.cantidad;
            const descuentoLinea = linea.descuentoUnitario * linea.cantidad;
            const totalConDescuento = totalSinDescuento - descuentoLinea;
            descuentoTotal += descuentoLinea;

            if (itbisGlobalActivado) {
                const base = totalConDescuento / (1 + itbisPorcentaje / 100);
                const itbis = totalConDescuento - base;
                subtotalSinItbis += base;
                impuestoTotal += itbis;
            } else {
                subtotalSinItbis += totalConDescuento;
            }
        });

        const total = subtotalSinItbis + impuestoTotal;
        const fmt = (v) =>
            "RD$ " +
            v.toLocaleString("es-DO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

        document.getElementById("resumenSubtotal").textContent =
            fmt(subtotalSinItbis);
        document.getElementById("resumenDescuento").textContent =
            "-" + fmt(descuentoTotal);
        document.getElementById("resumenImpuesto").textContent =
            fmt(impuestoTotal);
        document.getElementById("resumenTotal").textContent = fmt(total);

        // Actualizar total en el botón cobrar
        const btnCobrar = document.getElementById("btnCobrar");
        if (btnCobrar) {
            btnCobrar.disabled = carrito.length === 0;
            btnCobrar.innerHTML =
                carrito.length === 0
                    ? '<i class="bi bi-cash-coin me-2"></i> Cobrar'
                    : `<i class="bi bi-cash-coin me-2"></i> Cobrar RD$ ${total.toLocaleString("es-DO", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }
    }

    function sincronizarInputsOcultos() {
        const contenedor = document.getElementById("lineasOcultas");
        if (!contenedor) return;
        contenedor.innerHTML = carrito
            .map(
                (linea, idx) => `
            <input type="hidden" name="lineas[${idx}][variante_id]" value="${linea.id}">
            <input type="hidden" name="lineas[${idx}][cantidad]" value="${linea.cantidad}">
        `,
            )
            .join("");
    }

    // ════════════════════════════════════════════════
    // MODAL DE COBRO
    // ════════════════════════════════════════════════

    function bindBtnCobrar() {
        const btn = document.getElementById("btnCobrar");
        if (!btn) return;

        btn.addEventListener("click", async function () {
            if (carrito.length === 0) return;

            btn.disabled = true;

            try {
                if (clienteActual?.id) {
                    const r = await fetch(
                        `/api/clientes/credito?cliente_id=${clienteActual.id}`,
                        { headers: { "X-CSRF-TOKEN": csrfToken } },
                    );
                    const data = await r.json();
                    clienteActual.tieneCredito = data.tiene_credito ?? false;
                    clienteActual.creditoDisponible = parseFloat(
                        data.credito_disponible ?? 0,
                    );
                    clienteActual.limiteCredito = parseFloat(
                        data.limite_credito ?? 0,
                    );
                    clienteActual.balanceCredito = parseFloat(
                        data.balance_credito ?? 0,
                    );
                }
            } catch {
                if (clienteActual) clienteActual.tieneCredito = false;
            } finally {
                btn.disabled = carrito.length === 0;
            }

            abrirModalCobro();
        });
    }

    function calcularTotal() {
        let subtotalSinItbis = 0;
        let impuestoTotal = 0;

        carrito.forEach((linea) => {
            const totalSinDescuento = linea.precio * linea.cantidad;
            const descuentoLinea = linea.descuentoUnitario * linea.cantidad;
            const totalConDescuento = totalSinDescuento - descuentoLinea;

            if (itbisGlobalActivado) {
                const base = totalConDescuento / (1 + itbisPorcentaje / 100);
                subtotalSinItbis += base;
                impuestoTotal += totalConDescuento - base;
            } else {
                subtotalSinItbis += totalConDescuento;
            }
        });

        return subtotalSinItbis + impuestoTotal;
    }

    function abrirModalCobro() {
        if (carrito.length === 0) return;

        const total = calcularTotal();

        // Métodos de pago normales (sin Crédito)
        let metodosHTML = tiposPagoDisponibles
            .filter((tipo) => tipo.nombre.toLowerCase() !== "crédito")
            .map(
                (tipo) => `
            <div class="cobro-metodo-item">
                <div class="cobro-metodo-nombre">${tipo.nombre}</div>
                <div class="cobro-metodo-monto-wrapper">
                    <span class="cobro-metodo-prefix">RD$</span>
                    <input type="number"
                           class="cobro-metodo-input"
                           data-tipo-id="${tipo.id}"
                           placeholder="0.00" min="0" step="0.01" value=""
                           oninput="VentasModule.actualizarCobro()">
                </div>
            </div>
        `,
            )
            .join("");

        // Agregar crédito SOLO si el cliente lo tiene activo
        if (
            clienteActual?.tieneCredito &&
            clienteActual?.creditoDisponible > 0
        ) {
            const disponible = clienteActual.creditoDisponible.toLocaleString(
                "es-DO",
                {
                    minimumFractionDigits: 2,
                },
            );
            const tipoCreditoId =
                tiposPagoDisponibles.find(
                    (t) => t.nombre.toLowerCase() === "crédito",
                )?.id ?? "credito";

            metodosHTML += `
            <div class="cobro-metodo-item cobro-metodo-credito">
                <div>
                    <div class="cobro-metodo-nombre">
                         Crédito
                    </div>
                    <div style="font-size:11px; color:var(--text-muted);">
                        Disponible: RD$ ${disponible}
                    </div>
                </div>
                <div class="cobro-metodo-monto-wrapper">
                    <span class="cobro-metodo-prefix">RD$</span>
                    <input type="number"
                           class="cobro-metodo-input cobro-input-credito"
                           data-tipo-id="${tipoCreditoId}"
                           placeholder="0.00" min="0" step="0.01"
                           max="${clienteActual.creditoDisponible}"
                           value=""
                           oninput="VentasModule.validarMontoCredito(this); VentasModule.actualizarCobro()">
                </div>
            </div>
        `;
        }

        const modal = document.createElement("div");
        modal.className = "cobro-modal-overlay";
        modal.id = "cobroModalOverlay";
        modal.innerHTML = `
        <div class="cobro-modal">
            <div class="cobro-modal-header">
                <h6 class="fw-semibold mb-0">Cobrar venta</h6>
                <button type="button" class="btn-eliminar-carrito" onclick="VentasModule.cerrarModalCobro()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="cobro-modal-body">
                <div class="cobro-total-display">
                    <div class="cobro-total-label">Total a cobrar</div>
                    <div class="cobro-total-monto">RD$ ${total.toLocaleString("es-DO", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                </div>

                <div id="cobroRestante" class="cobro-restante-display">
                    Pendiente: <strong>RD$ ${total.toLocaleString("es-DO", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong>
                </div>

                <div class="cobro-metodos-lista">
                    ${metodosHTML}
                </div>

                <div id="cobroCambioBox" style="display:none;" class="cobro-cambio-box">
                    <div class="cobro-cambio-label">Cambio a entregar</div>
                    <div id="cobroCambioValor" class="cobro-cambio-valor">RD$ 0.00</div>
                </div>
            </div>
            <div class="cobro-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="VentasModule.cerrarModalCobro()">
                    Cancelar
                </button>
                <button type="button" id="btnConfirmarCobro"
                        class="btn btn-primary btn-confirmar"
                        onclick="VentasModule.confirmarCobro()"
                        disabled>
                    <i class="bi bi-check-circle me-1"></i> Confirmar venta
                </button>
            </div>
        </div>
    `;

        document.body.appendChild(modal);
    }

    function cerrarModalCobro() {
        document.getElementById("cobroModalOverlay")?.remove();
    }

    function actualizarCobro() {
        const total = calcularTotal();
        const inputs = document.querySelectorAll(".cobro-metodo-input");

        const inputCredito = document.querySelector(".cobro-input-credito");

        // Calcular lo pagado con métodos normales (sin crédito)
        let pagadoNormal = 0;
        inputs.forEach((input) => {
            if (input !== inputCredito) {
                pagadoNormal += parseFloat(input.value) || 0;
            }
        });

        // Autocompletar crédito con el saldo restante
        if (inputCredito) {
            const maxCredito = Math.min(
                clienteActual?.creditoDisponible ?? 0,
                Math.max(0, total - pagadoNormal),
            );
            inputCredito.max = maxCredito.toFixed(2);

            const autoValor = Math.min(
                Math.max(0, total - pagadoNormal),
                maxCredito,
            );
            inputCredito.value = autoValor > 0.001 ? autoValor.toFixed(2) : "";
        }

        // Total pagado incluyendo crédito autocompletado
        let pagadoTotal = 0;
        inputs.forEach((input) => {
            pagadoTotal += parseFloat(input.value) || 0;
        });

        const restante = total - pagadoTotal;

        const restanteEl = document.getElementById("cobroRestante");
        const cambioBox = document.getElementById("cobroCambioBox");
        const cambioVal = document.getElementById("cobroCambioValor");
        const btnConfirmar = document.getElementById("btnConfirmarCobro");

        const fmt = (v) =>
            "RD$ " +
            v.toLocaleString("es-DO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

        if (Math.abs(restante) <= 0.01 && pagadoTotal > 0) {
            restanteEl.className = "cobro-restante-display completo";
            restanteEl.innerHTML = `<strong>✓ Pago completo</strong>`;
            cambioBox.style.display = "none";
            btnConfirmar.disabled = false;
        } else if (restante > 0.01) {
            restanteEl.className = "cobro-restante-display";
            restanteEl.innerHTML = `Pendiente: <strong>${fmt(restante)}</strong>`;
            cambioBox.style.display = "none";
            btnConfirmar.disabled = true;
        } else {
            restanteEl.className = "cobro-restante-display completo";
            restanteEl.innerHTML = `<strong>✓ Pago completo</strong>`;
            cambioBox.style.display = "block";
            cambioVal.textContent = fmt(Math.abs(restante));
            btnConfirmar.disabled = false;
        }
    }
    function confirmarCobro() {
        const pagoInputs = document.querySelectorAll(".cobro-metodo-input");
        const pagosContainer = document.getElementById("pagosHiddenContainer");
        if (!pagosContainer) return;

        const total = calcularTotal();
        let pagadoTotal = 0;
        pagoInputs.forEach((input) => {
            pagadoTotal += parseFloat(input.value) || 0;
        });

        const restante = total - pagadoTotal;

        // Si hay saldo pendiente y el cliente tiene crédito, autocompletar
        if (restante > 0.01) {
            const inputCredito = document.querySelector(".cobro-input-credito");
            if (inputCredito && clienteActual?.tieneCredito) {
                const maxCredito = parseFloat(inputCredito.max) || 0;
                const montoActual = parseFloat(inputCredito.value) || 0;
                const completar = Math.min(restante, maxCredito - montoActual);

                if (completar > 0.01) {
                    inputCredito.value = (montoActual + completar).toFixed(2);
                    actualizarCobro();
                    // Volver a verificar si ya está completo
                    let nuevoPagado = 0;
                    pagoInputs.forEach((input) => {
                        nuevoPagado += parseFloat(input.value) || 0;
                    });
                    if (Math.abs(total - nuevoPagado) > 0.01) {
                        alert(
                            "Crédito insuficiente para cubrir el saldo pendiente.",
                        );
                        return;
                    }
                } else {
                    alert(
                        "El monto pagado no puede ser menor al total de la venta.",
                    );
                    return;
                }
            } else {
                alert(
                    "El monto pagado no puede ser menor al total de la venta.",
                );
                return;
            }
        }

        pagosContainer.innerHTML = "";
        let idx = 0;

        pagoInputs.forEach((input) => {
            const monto = parseFloat(input.value) || 0;
            if (monto <= 0) return;

            pagosContainer.innerHTML += `
            <input type="hidden" name="pagos[${idx}][tipo_pago_id]" value="${input.dataset.tipoId}">
            <input type="hidden" name="pagos[${idx}][monto]" value="${monto.toFixed(2)}">
        `;
            idx++;
        });

        if (idx === 0) {
            alert("Ingresa al menos un monto de pago.");
            return;
        }

        cerrarModalCobro();
        document.getElementById("formVenta")?.submit();
    }

    function validarMontoCredito(input) {
        const max = parseFloat(input.max) || 0;
        const val = parseFloat(input.value) || 0;
        if (val > max) {
            input.value = max.toFixed(2);
            alert(`El monto máximo a crédito es RD$ ${max.toFixed(2)}`);
        }
    }
    // ── Submit del formulario ────────────────────────
    function bindFormSubmit() {
        document
            .getElementById("formVenta")
            ?.addEventListener("submit", function (e) {
                if (carrito.length === 0) {
                    e.preventDefault();
                    alert("Agrega al menos un producto al carrito.");
                }
            });
    }

    // ════════════════════════════════════════════════
    // TPV — CATEGORÍAS Y GRID DE PRODUCTOS
    // ════════════════════════════════════════════════
    let categoriaActual = null;

    function initCategorias() {
        const lista = document.getElementById("tpvCategoriasList");
        if (!lista) return;

        fetch("/api/tpv/categorias", {
            headers: { "X-CSRF-TOKEN": csrfToken },
        })
            .then((r) => r.json())
            .then((categorias) => {
                lista.innerHTML = categorias
                    .map(
                        (c) => `
                <button type="button" class="tpv-categoria-btn"
                        data-id="${c.id}"
                        onclick="VentasModule.seleccionarCategoria(${c.id}, '${c.nombre.replace(/'/g, "\\'")}', this)">
                    ${c.nombre}
                </button>
            `,
                    )
                    .join("");
            })
            .catch(() => {
                lista.innerHTML = `<span style="font-size:12px; color:var(--text-muted);">Error al cargar categorías.</span>`;
            });
    }

    function seleccionarCategoria(id, nombre, btn) {
        document
            .querySelectorAll(".tpv-categoria-btn")
            .forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");
        categoriaActual = id;
        cargarProductosCategoria(id);
    }

    function cargarProductosCategoria(categoriaId) {
        const grid = document.getElementById("tpvProductosGrid");
        if (!grid) return;
        grid.innerHTML = `<div class="tpv-productos-loading"><i class="bi bi-arrow-repeat me-1"></i>Cargando...</div>`;

        fetch(`/api/tpv/productos-categoria?categoria_id=${categoriaId}`, {
            headers: { "X-CSRF-TOKEN": csrfToken },
        })
            .then((r) => r.json())
            .then((variantes) => {
                if (variantes.length === 0) {
                    grid.innerHTML = `<div class="tpv-productos-vacio">
                    <i class="bi bi-box-seam" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                    No hay productos disponibles en esta categoría.
                </div>`;
                    return;
                }
                grid.innerHTML = variantes
                    .map((v) => {
                        const agotado = v.disponible <= 0;
                        const nivelClase = agotado
                            ? "agotado"
                            : v.disponible <= 5
                              ? "bajo"
                              : "ok";
                        const nivelTexto = agotado
                            ? "Agotado"
                            : v.disponible <= 5
                              ? `Quedan ${v.disponible}`
                              : `${v.disponible} disp.`;
                        const partes = v.texto.split(" — ");
                        const nombre = partes[0] ?? v.texto;
                        const atributos = partes[1] ?? "";
                        return `
                    <div class="tpv-producto-card ${agotado ? "agotado" : ""}" 
                         onclick="${agotado ? "" : `VentasModule.agregarDesdeGrid(${JSON.stringify(v).replace(/"/g, "&quot;")})`}">
                        <div class="tpv-producto-nombre">${nombre}</div>
                        ${atributos ? `<div class="tpv-producto-atributos">${atributos}</div>` : ""}
                        <div class="tpv-producto-precio">RD$ ${parseFloat(v.precio).toLocaleString("es-DO", { minimumFractionDigits: 2 })}</div>
                        <div class="tpv-producto-stock ${nivelClase}">${nivelTexto}</div>
                    </div>
                `;
                    })
                    .join("");
            });
    }

    function agregarDesdeGrid(item) {
        agregarAlCarrito(item);
    }

    // ── Control del Tab (Carrito / Catálogo) ─────────
    function mostrarTab(tab) {
        const panelCarrito = document.getElementById("panelCarrito");
        const panelCatalogo = document.getElementById("panelCatalogo");
        const tabCarrito = document.getElementById("tabCarrito");
        const tabCatalogo = document.getElementById("tabCatalogo");

        if (tab === "carrito") {
            panelCarrito.style.display = "flex";
            panelCatalogo.style.display = "none";
            tabCarrito.style.background = "#1f2125";
            tabCarrito.style.color = "#fff";
            tabCarrito.style.borderColor = "#1f2125";
            tabCatalogo.style.background = "var(--bg-elevated)";
            tabCatalogo.style.color = "var(--text-secondary)";
            tabCatalogo.style.borderColor = "var(--border)";
        } else {
            panelCarrito.style.display = "none";
            panelCatalogo.style.display = "flex";
            tabCatalogo.style.background = "#1f2125";
            tabCatalogo.style.color = "#fff";
            tabCatalogo.style.borderColor = "#1f2125";
            tabCarrito.style.background = "var(--bg-elevated)";
            tabCarrito.style.color = "var(--text-secondary)";
            tabCarrito.style.borderColor = "var(--border)";
        }
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
        abrirModalCobro,
        cerrarModalCobro,
        actualizarCobro,
        confirmarCobro,
        seleccionarCategoria,
        agregarDesdeGrid,
        mostrarTab,
        validarMontoCredito,
    };
})();

window.VentasModule = VentasModule;
document.addEventListener("DOMContentLoaded", () => VentasModule.init());
