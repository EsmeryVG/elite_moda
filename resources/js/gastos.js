/* =====================================================
   Gastos / Caja Chica — JS del módulo
   ===================================================== */

const GastosModule = (function () {
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;
    const baseUrl = document.getElementById("tablaContainer")?.dataset.url;

    let buscarTimeout = null;
    let origenActual =
        new URLSearchParams(window.location.search).get("origen") ?? "";

    function cargarTabla(params = {}) {
        const container = document.getElementById("tablaContainer");
        if (!container || !baseUrl) return;

        container.classList.add("loading");

        const url = new URL(baseUrl);
        if (params.origen) url.searchParams.set("origen", params.origen);
        if (params.categoria_gasto_id)
            url.searchParams.set(
                "categoria_gasto_id",
                params.categoria_gasto_id,
            );
        if (params.desde) url.searchParams.set("desde", params.desde);
        if (params.hasta) url.searchParams.set("hasta", params.hasta);
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
                cargarTabla({ origen: origenActual, page });
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
                origenActual = this.dataset.origen ?? "";
                cargarTabla({ origen: origenActual });
            });
        });
    }

    function initSelectorCategoria(elId) {
        const el = document.getElementById(elId);
        if (!el || typeof TomSelect === "undefined") return;

        new TomSelect(el, {
            create: function (input, callback) {
                fetch("/categorias-gasto", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                    },
                    body: JSON.stringify({ nombre: input }),
                })
                    .then((res) => {
                        if (res.status === 403) {
                            alert(
                                "No tienes permiso para crear categorías nuevas. Contacta a un administrador.",
                            );
                            return null;
                        }
                        return res.json();
                    })
                    .then((data) => {
                        if (!data || !data.id) {
                            if (data)
                                alert(
                                    data.message ??
                                        "Error al crear la categoría.",
                                );
                            callback();
                            return;
                        }
                        callback({ value: data.id, text: data.nombre });
                    })
                    .catch(() => {
                        alert("Error de conexión.");
                        callback();
                    });
            },
            createFilter: (input) => input.trim().length > 0,
            placeholder: "Busca o crea una categoría...",
            render: {
                option_create(data, escape) {
                    return `<div class="create"><i class="bi bi-plus-circle me-1"></i> Crear <strong>${escape(data.input)}</strong></div>`;
                },
            },
        });
    }

    function bindFiltrosFecha() {
        const form = document.getElementById("formFiltrosFecha");
        if (!form) return;
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            cargarTabla({
                origen: origenActual,
                desde: document.getElementById("filtroDesde")?.value,
                hasta: document.getElementById("filtroHasta")?.value,
            });
        });
    }

    function init() {
        bindFiltros();
        bindPaginacion();
        bindFiltrosFecha();
        initSelectorCategoria("selectCategoriaGasto");
        initSelectorCategoria("selectCategoriaGastoCC"); // si aplica en caja_chica
    }
    return { init };
})();

window.GastosModule = GastosModule;
document.addEventListener("DOMContentLoaded", () => GastosModule.init());
