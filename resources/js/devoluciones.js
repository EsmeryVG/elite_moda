/* =====================================================
   Devoluciones — JS del módulo
   ===================================================== */

const DevolucionesModule = (function () {
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;
    const baseUrl = document.getElementById("tablaContainer")?.dataset.url;

    let buscarTimeout = null;
    let estadoActual =
        new URLSearchParams(window.location.search).get("estado") ?? "";
    let buscarActual =
        new URLSearchParams(window.location.search).get("buscar") ?? "";

    // ════════════════════════════════════════════════
    // ÍNDICE — AJAX
    // ════════════════════════════════════════════════

    function cargarTabla(params = {}) {
        const container = document.getElementById("tablaContainer");
        if (!container || !baseUrl) return;

        container.classList.add("loading");

        const url = new URL(baseUrl);
        if (params.buscar) url.searchParams.set("busqueda", params.buscar);
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
                const page = new URL(this.href).searchParams.get("page") ?? 1;
                cargarTabla({
                    buscar: buscarActual,
                    estado: estadoActual,
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
                cargarTabla({ buscar: buscarActual, estado: estadoActual });
            });
        });
    }

    function bindBuscador() {
        const input = document.getElementById("buscadorDevoluciones");
        if (!input) return;
        input.addEventListener("input", function () {
            clearTimeout(buscarTimeout);
            buscarActual = this.value.trim();
            buscarTimeout = setTimeout(() => {
                cargarTabla({ buscar: buscarActual, estado: estadoActual });
            }, 400);
        });
    }

    // ════════════════════════════════════════════════
    // FORMULARIO CREATE — selección de líneas + empleado
    // ════════════════════════════════════════════════

    function initFormulario() {
        const form = document.getElementById("formDevolucion");
        if (!form) return;

        initTomSelectEmpleado();

        document.querySelectorAll(".linea-devolucion-check").forEach((chk) => {
            chk.addEventListener("change", function () {
                const card = this.closest(".linea-devolucion-card");
                card.querySelectorAll("input, select").forEach((el) => {
                    if (el !== this) el.disabled = !this.checked;
                });
                card.classList.toggle("linea-activa", this.checked);
                actualizarResumen();
            });
        });

        document
            .querySelectorAll(".cantidad-devolver, .motivo-devolucion")
            .forEach((el) => {
                el.addEventListener("input", actualizarResumen);
            });

        form.addEventListener("submit", function (e) {
            const empleadoSeleccionado =
                document.getElementById("selectEmpleado")?.value;
            if (!empleadoSeleccionado) {
                e.preventDefault();
                alert(
                    "Debes seleccionar el empleado que atiende la devolución.",
                );
                return;
            }

            const algunaSeleccionada =
                document.querySelectorAll(".linea-devolucion-check:checked")
                    .length > 0;
            if (!algunaSeleccionada) {
                e.preventDefault();
                alert("Selecciona al menos un producto para devolver.");
            }
        });
    }

    function initBusquedaFactura() {
        const selectCliente = document.getElementById("selectClienteBusqueda");
        const selectProducto = document.getElementById(
            "selectProductoBusqueda",
        );
        if (
            !selectCliente ||
            !selectProducto ||
            typeof TomSelect === "undefined"
        )
            return;

        const tsCliente = new TomSelect(selectCliente, {
            valueField: "id",
            labelField: "texto",
            searchField: ["texto"],
            placeholder: "Buscar cliente...",
            preload: "focus",
            load(query, callback) {
                fetch(`/api/clientes/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                    .then((r) => r.json())
                    .then((data) => callback(data))
                    .catch(() => callback());
            },
        });

        const tsProducto = new TomSelect(selectProducto, {
            valueField: "id",
            labelField: "texto",
            searchField: ["texto"],
            placeholder: "Primero selecciona un cliente...",
            load(query, callback) {
                const clienteId = tsCliente.getValue();
                if (!clienteId) return callback();
                fetch(
                    `/api/devoluciones/productos-cliente?cliente_id=${clienteId}&q=${encodeURIComponent(query)}`,
                    {
                        headers: { "X-CSRF-TOKEN": csrfToken },
                    },
                )
                    .then((r) => r.json())
                    .then((data) => callback(data))
                    .catch(() => callback());
            },
        });

        tsCliente.on("change", function () {
            tsProducto.clear();
            tsProducto.clearOptions();
            tsProducto.settings.placeholder = "Buscar producto...";
            tsProducto.inputState();
        });
    }

    function initTomSelectEmpleado() {
        const el = document.getElementById("selectEmpleado");
        if (!el || typeof TomSelect === "undefined") return;

        new TomSelect(el, {
            valueField: "id",
            labelField: "texto",
            searchField: ["texto"],
            placeholder: "Buscar empleado...",
            preload: "focus",
            load(query, callback) {
                fetch(`/api/empleados/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                    .then((r) => r.json())
                    .then((data) => callback(data))
                    .catch(() => callback());
            },
        });
    }

    function actualizarResumen() {
        let totalLineas = 0;
        document
            .querySelectorAll(".linea-devolucion-check:checked")
            .forEach((chk) => {
                const card = chk.closest(".linea-devolucion-card");
                const precio = parseFloat(card.dataset.precio) || 0;
                const cantidad =
                    parseInt(card.querySelector(".cantidad-devolver")?.value) ||
                    0;
                totalLineas += precio * cantidad;
            });

        const resumen = document.getElementById("resumenTotalDevolucion");
        if (resumen) {
            resumen.textContent = "RD$ " + totalLineas.toFixed(2);
        }
    }

    // ════════════════════════════════════════════════
    // SHOW — inspección por línea
    // ════════════════════════════════════════════════

    function initInspeccion() {
        document.querySelectorAll(".btn-inspeccionar").forEach((btn) => {
            btn.addEventListener("click", function () {
                const condicion = this.dataset.condicion;
                const mensaje =
                    condicion === "conforme"
                        ? "¿Confirmar que el producto está conforme? Se restaurará al stock."
                        : "¿Confirmar que el producto no está conforme? Se registrará como merma.";

                if (!confirm(mensaje)) return;

                const url = this.dataset.url;
                const formData = new FormData();
                formData.append("_token", csrfToken);
                formData.append("condicion", condicion);

                fetch(url, {
                    method: "POST",
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                    body: formData,
                })
                    .then((res) => res.text())
                    .then(() => window.location.reload())
                    .catch(() =>
                        alert("Ocurrió un error al inspeccionar la línea."),
                    );
            });
        });
    }

    // ════════════════════════════════════════════════
    // INIT
    // ════════════════════════════════════════════════

    function init() {
        bindFiltros();
        bindBuscador();
        bindPaginacion();
        initFormulario();
        initInspeccion();
        initBusquedaFactura();
    }

    return { init };
})();

window.DevolucionesModule = DevolucionesModule;
document.addEventListener("DOMContentLoaded", () => DevolucionesModule.init());
