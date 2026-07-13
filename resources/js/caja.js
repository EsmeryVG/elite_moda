/* =====================================================
   Caja / Sesiones de Caja — JS del módulo
   ===================================================== */

const CajaModule = (function () {
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;
    const baseUrl = document.getElementById("tablaContainer")?.dataset.url;

    let buscarTimeout = null;
    let estadoActual =
        new URLSearchParams(window.location.search).get("estado") ?? "";

    // ════════════════════════════════════════════════
    // ÍNDICE — AJAX
    // ════════════════════════════════════════════════

    function cargarTabla(params = {}) {
        const container = document.getElementById("tablaContainer");
        if (!container || !baseUrl) return;

        container.classList.add("loading");

        const url = new URL(baseUrl);
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
                cargarTabla({ estado: estadoActual, page });
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
                cargarTabla({ estado: estadoActual });
            });
        });
    }

    // ════════════════════════════════════════════════
    // ABRIR SESIÓN — selección visual de caja
    // ════════════════════════════════════════════════

    function initFormularioAbrir() {
        document.querySelectorAll(".caja-select-card").forEach((card) => {
            card.addEventListener("click", function () {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;

                document
                    .querySelectorAll(".caja-select-card")
                    .forEach((c) => c.classList.remove("seleccionada"));
                this.classList.add("seleccionada");
            });
        });
    }

    // ════════════════════════════════════════════════
    // CERRAR SESIÓN — cálculo de diferencia en vivo
    // ════════════════════════════════════════════════

    function initFormularioCerrar() {
        const input = document.getElementById("montoCierreReal");
        if (!input) return;

        const montoEsperado = parseFloat(input.dataset.esperado) || 0;

        input.addEventListener("input", function () {
            const real = parseFloat(this.value) || 0;
            const diferencia = real - montoEsperado;

            const span = document.getElementById("diferenciaPreview");
            const campoObservacion =
                document.getElementById("grupoObservacion");

            if (span) {
                span.textContent =
                    (diferencia > 0 ? "+" : "") +
                    "RD$ " +
                    diferencia.toFixed(2);
                span.classList.remove(
                    "cierre-diferencia-positiva",
                    "cierre-diferencia-negativa",
                    "cierre-diferencia-neutra",
                );
                span.classList.add(
                    diferencia > 0
                        ? "cierre-diferencia-positiva"
                        : diferencia < 0
                          ? "cierre-diferencia-negativa"
                          : "cierre-diferencia-neutra",
                );
            }

            if (campoObservacion) {
                campoObservacion.style.display =
                    diferencia !== 0 ? "block" : "none";
            }
        });
    }

    // ════════════════════════════════════════════════
    // INIT
    // ════════════════════════════════════════════════

    function init() {
        bindFiltros();
        bindPaginacion();
        initFormularioAbrir();
        initFormularioCerrar();
    }

    return { init };
})();

window.CajaModule = CajaModule;
document.addEventListener("DOMContentLoaded", () => CajaModule.init());
