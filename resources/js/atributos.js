/* =====================================================
   Atributos — JS del módulo
   ===================================================== */

const AtributosModule = (function () {
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;
    const baseUrl = document.getElementById("listaAtributos")?.dataset.url;

    let buscarTimeout = null;
    let estadoActual =
        new URLSearchParams(window.location.search).get("estado") ?? "";
    let buscarActual =
        new URLSearchParams(window.location.search).get("buscar") ?? "";

    // ── AJAX lista de atributos ─────────────────────
    function cargarLista(params = {}) {
        const container = document.getElementById("listaAtributos");
        if (!container) return;

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
                const page = new URL(this.href).searchParams.get("page") ?? 1;
                cargarLista({
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
                estadoActual = this.dataset.estado;
                buscarActual =
                    document
                        .getElementById("buscadorAtributos")
                        ?.value.trim() ?? "";
                cargarLista({ buscar: buscarActual, estado: estadoActual });
            });
        });
    }

    function bindBuscador() {
        const input = document.getElementById("buscadorAtributos");
        if (!input) return;
        input.addEventListener("input", function () {
            clearTimeout(buscarTimeout);
            buscarActual = this.value.trim();
            buscarTimeout = setTimeout(() => {
                cargarLista({ buscar: buscarActual, estado: estadoActual });
            }, 400);
        });
    }

    // ── Expandir / colapsar atributo ────────────────
    function bindExpandir() {
        document
            .querySelectorAll(".atributo-card__header")
            .forEach((header) => {
                header.addEventListener("click", function (e) {
                    // No expandir si el click fue en un botón de acción
                    if (e.target.closest(".atributo-card__actions")) return;
                    const card = this.closest(".atributo-card");
                    card.classList.toggle("open");
                });
            });
    }

    // ── Modal editar atributo ───────────────────────
    // ── Modal editar/crear atributo ─────────────────
    function bindModalAtributo() {
        // Abrir modal de NUEVO atributo
        document
            .querySelectorAll('[data-open-modal="nuevoAtributo"]')
            .forEach((btn) => {
                btn.addEventListener("click", function () {
                    document
                        .getElementById("modalNuevoAtributo")
                        .classList.add("open");
                    document
                        .querySelector(
                            '#modalNuevoAtributo input[name="nombre"]',
                        )
                        ?.focus();
                });
            });

        // Abrir modal EDITAR
        document
            .querySelectorAll('[data-open-modal="editarAtributo"]')
            .forEach((btn) => {
                btn.addEventListener("click", function (e) {
                    e.stopPropagation();
                    const id = this.dataset.id;
                    const nombre = this.dataset.nombre;

                    document.getElementById("modalAtributoId").value = id;
                    document.getElementById("modalAtributoNombre").value =
                        nombre;
                    document.getElementById("formEditarAtributo").action =
                        `/atributos/${id}`;

                    document
                        .getElementById("modalEditarAtributo")
                        .classList.add("open");
                    document.getElementById("modalAtributoNombre").focus();
                });
            });

        // Cerrar modal
        document.querySelectorAll("[data-close-modal]").forEach((btn) => {
            btn.addEventListener("click", function () {
                const modalId = this.dataset.closeModal;
                document.getElementById(modalId)?.classList.remove("open");
            });
        });

        // Cerrar al hacer clic en overlay
        document.querySelectorAll(".em-modal-overlay").forEach((overlay) => {
            overlay.addEventListener("click", function (e) {
                if (e.target === this) this.classList.remove("open");
            });
        });
    }

    // ── Modal editar valor ──────────────────────────
    function bindModalValor() {
        document
            .querySelectorAll('[data-open-modal="editarValor"]')
            .forEach((btn) => {
                btn.addEventListener("click", function (e) {
                    e.stopPropagation();
                    const atributoId = this.dataset.atributoId;
                    const valorId = this.dataset.valorId;
                    const valor = this.dataset.valor;
                    const orden = this.dataset.orden;

                    document.getElementById("modalValorTexto").value = valor;
                    document.getElementById("modalValorOrden").value = orden;
                    document.getElementById("formEditarValor").action =
                        `/atributos/${atributoId}/valores/${valorId}`;

                    document
                        .getElementById("modalEditarValor")
                        .classList.add("open");
                    document.getElementById("modalValorTexto").focus();
                });
            });
    }

    // ── Init ────────────────────────────────────────
    function init() {
        if (
            !document.getElementById("listaAtributos") &&
            !document.querySelector(".atributo-card")
        )
            return;

        bindFiltros();
        bindBuscador();
        bindPaginacion();
        bindExpandir();
        bindModalAtributo();
        bindModalValor();
    }

    return { init };
})();

document.addEventListener("DOMContentLoaded", () => AtributosModule.init());
