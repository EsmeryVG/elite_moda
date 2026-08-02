/* =====================================================
   Órdenes de Compra — JS del módulo
   ===================================================== */

const OrdenesCompraModule = (function () {
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;
    const baseUrl = document.getElementById("tablaContainer")?.dataset.url;
    const itbisPorcentaje = parseFloat(
        document.getElementById("itbisPorcentajeData")?.value ?? 18,
    );

    let buscarTimeout = null;
    let estadoActual =
        new URLSearchParams(window.location.search).get("estado") ?? "";
    let proveedorActual =
        new URLSearchParams(window.location.search).get("proveedor") ?? "";
    let buscarActual =
        new URLSearchParams(window.location.search).get("buscar") ?? "";
    let contadorLineas = 0;

    // ════════════════════════════════════════════════
    // ÍNDICE — AJAX
    // ════════════════════════════════════════════════

    function cargarTabla(params = {}) {
        const container = document.getElementById("tablaContainer");
        if (!container || !baseUrl) return;

        container.classList.add("loading");

        const url = new URL(baseUrl);
        if (params.buscar) url.searchParams.set("buscar", params.buscar);
        if (params.estado) url.searchParams.set("estado", params.estado);
        if (params.proveedor)
            url.searchParams.set("proveedor", params.proveedor);
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
                const page = new URL(this.href).searchParams.get("page") ?? 1;
                cargarTabla({
                    buscar: buscarActual,
                    estado: estadoActual,
                    proveedor: proveedorActual,
                    page,
                });
            });
        });
    }

    function bindFiltros() {
        document.querySelectorAll(".em-filtro").forEach((btn) => {
            btn.addEventListener("click", function () {
                document
                    .querySelectorAll(".em-filtro")
                    .forEach((b) => b.classList.remove("active"));
                this.classList.add("active");
                estadoActual = this.dataset.estado ?? "";
                cargarTabla({
                    buscar: buscarActual,
                    estado: estadoActual,
                    proveedor: proveedorActual,
                });
            });
        });

        document
            .getElementById("filtroProveedor")
            ?.addEventListener("change", function () {
                proveedorActual = this.value;
                cargarTabla({
                    buscar: buscarActual,
                    estado: estadoActual,
                    proveedor: proveedorActual,
                });
            });
    }

    function bindBuscador() {
        const input = document.getElementById("buscadorOrdenes");
        if (!input) return;
        input.addEventListener("input", function () {
            clearTimeout(buscarTimeout);
            buscarActual = this.value.trim();
            buscarTimeout = setTimeout(() => {
                cargarTabla({
                    buscar: buscarActual,
                    estado: estadoActual,
                    proveedor: proveedorActual,
                });
            }, 400);
        });
    }

    // ════════════════════════════════════════════════
    // FORMULARIO — LÍNEAS DINÁMICAS
    // ════════════════════════════════════════════════

    function initFormulario() {
        const contenedor = document.getElementById("contenedorLineas");
        if (!contenedor) return;
        document
            .getElementById("btnAgregarLinea")
            ?.addEventListener("click", () => {
                agregarLinea();
            });
        const lineasData = contenedor.dataset.lineas;
        if (lineasData) {
            try {
                const lineas = JSON.parse(lineasData);
                lineas.forEach((linea) => agregarLinea(linea));
            } catch (e) {
                console.error("Error al parsear líneas iniciales:", e);
            }
        }
        actualizarTotales();
    }

    function agregarLinea(datos = null) {
        const contenedor = document.getElementById("contenedorLineas");
        if (!contenedor) return;

        const idx = contadorLineas++;

        const card = document.createElement("div");
        card.className = "linea-card";
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
                        placeholder="Buscar por nombre, código o código de barras...">
                </select>
            </div>

            <div class="linea-card-body">
                <div>
                    <label style="font-size:11px; font-weight:600; text-transform:uppercase;
                                  letter-spacing:0.06em; color:var(--text-muted); margin-bottom:6px;
                                  display:block;">
                        Cantidad *
                    </label>
                    <input type="number"
                           name="lineas[${idx}][cantidad_solicitada]"
                           class="form-control linea-cantidad"
                           value="${datos?.cantidad ?? 1}"
                           min="1" step="1"
                           oninput="OrdenesCompraModule.actualizarSubtotal(${idx})">
                </div>
                <div>
                    <label style="font-size:11px; font-weight:600; text-transform:uppercase;
                                  letter-spacing:0.06em; color:var(--text-muted); margin-bottom:6px;
                                  display:block;">
                        Precio unitario *
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"
                              style="background:var(--bg-elevated); border-color:var(--border);
                                     color:var(--text-muted); font-size:13px;">RD$</span>
                        <input type="number"
                               name="lineas[${idx}][precio_unitario]"
                               class="form-control linea-precio"
                               value="${datos?.precio ?? ""}"
                               min="0" step="0.01" placeholder="0.00"
                               oninput="OrdenesCompraModule.actualizarSubtotal(${idx})">
                    </div>
                </div>
            </div>

            <div class="linea-card-footer">
                <div class="form-check">
                    <input class="form-check-input linea-itbis"
                           type="checkbox"
                           name="lineas[${idx}][itbis_incluido]"
                           id="itbis-${idx}" value="1"
                           ${datos?.itbisIncluido !== false ? "checked" : ""}
                           onchange="OrdenesCompraModule.actualizarSubtotal(${idx})">
                    <label class="form-check-label" for="itbis-${idx}"
                           style="font-size:12.5px; color:var(--text-secondary);">
                        Precio incluye ITBIS (${itbisPorcentaje}%)
                    </label>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <span class="linea-subtotal-label">Subtotal: </span>
                        <span class="linea-subtotal-valor linea-subtotal" data-idx="${idx}">
                            RD$ 0.00
                        </span>
                    </div>
                    <button type="button" class="btn-eliminar-linea"
                            onclick="OrdenesCompraModule.eliminarLinea(${idx})">
                        <i class="bi bi-trash3 me-1"></i> Eliminar
                    </button>
                </div>
            </div>
        `;

        contenedor.appendChild(card);

        initTomSelectVariante(idx, datos?.varianteId, datos?.varianteTexto);

        if (datos?.cantidad && datos?.precio) {
            actualizarSubtotal(idx);
        }
    }

    function initTomSelectVariante(
        idx,
        valorInicial = null,
        textoInicial = null,
    ) {
        const el = document.getElementById(`selectVariante-${idx}`);
        if (!el || typeof TomSelect === "undefined") return;

        const ts = new TomSelect(el, {
            valueField: "id",
            labelField: "texto",
            searchField: ["texto", "codigo"],
            placeholder: "Buscar por nombre, código o código de barras...",
            load(query, callback) {
                if (query.length < 2) return callback();
                fetch(`/api/variantes/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                    .then((r) => r.json())
                    .then((data) => callback(data))
                    .catch(() => callback());
            },
            onChange(value) {
                const item = ts.options[value];
                if (item?.precio) {
                    const card = document.querySelector(
                        `.linea-card[data-idx="${idx}"]`,
                    );
                    const input = card?.querySelector(".linea-precio");
                    if (input && !input.value) {
                        input.value = item.precio;
                        actualizarSubtotal(idx);
                    }
                }
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

        if (valorInicial && textoInicial) {
            ts.addOption({ id: valorInicial, texto: textoInicial, codigo: "" });
            ts.setValue(valorInicial);
        }
    }

    function eliminarLinea(idx) {
        document.querySelector(`.linea-card[data-idx="${idx}"]`)?.remove();
        actualizarTotales();
    }

    function actualizarSubtotal(idx) {
        const card = document.querySelector(`.linea-card[data-idx="${idx}"]`);
        if (!card) return;

        const cantidad =
            parseFloat(card.querySelector(".linea-cantidad")?.value) || 0;
        const precio =
            parseFloat(card.querySelector(".linea-precio")?.value) || 0;
        const subtotal = cantidad * precio;

        const span = card.querySelector(`.linea-subtotal[data-idx="${idx}"]`);
        if (span) {
            span.textContent =
                "RD$ " +
                subtotal.toLocaleString("es-DO", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
        }

        actualizarTotales();
    }

    function actualizarTotales() {
        let subtotalSinItbis = 0;
        let impuestoTotal = 0;

        document.querySelectorAll(".linea-card").forEach((card) => {
            const cantidad =
                parseFloat(card.querySelector(".linea-cantidad")?.value) || 0;
            const precio =
                parseFloat(card.querySelector(".linea-precio")?.value) || 0;
            const itbisIncluido =
                card.querySelector(".linea-itbis")?.checked ?? false;

            const lineaTotal = cantidad * precio;

            if (itbisIncluido) {
                const precioBase = precio / (1 + itbisPorcentaje / 100);
                const itbisLinea = (precio - precioBase) * cantidad;
                subtotalSinItbis += lineaTotal - itbisLinea;
                impuestoTotal += itbisLinea;
            } else {
                subtotalSinItbis += lineaTotal;
            }
        });

        const total = subtotalSinItbis + impuestoTotal;

        const fmt = (val) =>
            "RD$ " +
            val.toLocaleString("es-DO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

        const elSubtotal = document.getElementById("resumenSubtotal");
        const elImpuesto = document.getElementById("resumenImpuesto");
        const elTotal = document.getElementById("resumenTotal");

        if (elSubtotal) elSubtotal.textContent = fmt(subtotalSinItbis);
        if (elImpuesto) elImpuesto.textContent = fmt(impuestoTotal);
        if (elTotal) elTotal.textContent = fmt(total);
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
        actualizarSubtotal,
        actualizarTotales,
    };
})();

window.OrdenesCompraModule = OrdenesCompraModule;
document.addEventListener("DOMContentLoaded", () => OrdenesCompraModule.init());
