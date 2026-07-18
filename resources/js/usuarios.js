/* =====================================================
   Usuarios — JS del módulo
   ===================================================== */

const UsuariosModule = (function () {
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;
    const baseUrl = document.getElementById("tablaContainer")?.dataset.url;

    let buscarTimeout = null;
    let estadoActual =
        new URLSearchParams(window.location.search).get("estado") ?? "";
    let rolActual =
        new URLSearchParams(window.location.search).get("rol") ?? "";
    let buscarActual =
        new URLSearchParams(window.location.search).get("buscar") ?? "";

    function cargarTabla(params = {}) {
        const container = document.getElementById("tablaContainer");
        if (!container || !baseUrl) return;

        container.classList.add("loading");

        const url = new URL(baseUrl);
        if (params.buscar) url.searchParams.set("buscar", params.buscar);
        if (params.estado) url.searchParams.set("estado", params.estado);
        if (params.rol) url.searchParams.set("rol", params.rol);
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
                    rol: rolActual,
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
                    rol: rolActual,
                });
            });
        });

        document
            .getElementById("filtroRol")
            ?.addEventListener("change", function () {
                rolActual = this.value;
                cargarTabla({
                    buscar: buscarActual,
                    estado: estadoActual,
                    rol: rolActual,
                });
            });
    }

    function bindBuscador() {
        const input = document.getElementById("buscadorUsuarios");
        if (!input) return;
        input.addEventListener("input", function () {
            clearTimeout(buscarTimeout);
            buscarActual = this.value.trim();
            buscarTimeout = setTimeout(() => {
                cargarTabla({
                    buscar: buscarActual,
                    estado: estadoActual,
                    rol: rolActual,
                });
            }, 400);
        });
    }

    // ── Gestión de permisos por rol (roles/edit) ─────
    function bindPermisos() {
        document.querySelectorAll(".check-modulo").forEach((chkModulo) => {
            chkModulo.addEventListener("change", function () {
                const modulo = this.dataset.modulo;
                document
                    .querySelectorAll(`.check-permiso[data-modulo="${modulo}"]`)
                    .forEach((chk) => {
                        chk.checked = this.checked;
                    });
            });
        });

        document
            .getElementById("btnMarcarTodos")
            ?.addEventListener("click", function () {
                document
                    .querySelectorAll(".check-permiso, .check-modulo")
                    .forEach((chk) => (chk.checked = true));
            });

        document
            .getElementById("btnDesmarcarTodos")
            ?.addEventListener("click", function () {
                document
                    .querySelectorAll(".check-permiso, .check-modulo")
                    .forEach((chk) => (chk.checked = false));
            });
    }

    function init() {
        bindFiltros();
        bindBuscador();
        bindPaginacion();
        bindPermisos();
    }

    return { init };
})();

document.addEventListener("DOMContentLoaded", () => UsuariosModule.init());
