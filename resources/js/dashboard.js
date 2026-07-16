/* =====================================================
   Dashboard — JS global del layout + gráficos
   ===================================================== */

import { Chart, registerables } from "chart.js";
Chart.register(...registerables);

document.addEventListener("DOMContentLoaded", () => {
    // ════════════════════════════════════════════════
    // UI GLOBAL DEL LAYOUT
    // ════════════════════════════════════════════════

    // ── Toggle del sidebar (colapsar/expandir) ───────
    const sidebarToggle = document.getElementById("sidebarToggle");
    const emLayout = document.getElementById("emLayout");
    const emOverlay = document.getElementById("emOverlay");

    sidebarToggle?.addEventListener("click", function () {
        emLayout?.classList.toggle("sidebar-collapsed");
        emLayout?.classList.toggle("sidebar-mobile-open");
    });

    emOverlay?.addEventListener("click", function () {
        emLayout?.classList.remove("sidebar-mobile-open");
    });

    // ── Dropdowns genéricos de la navbar (usuario, alertas) ──
    document.querySelectorAll('[id$="Btn"]').forEach((btn) => {
        const dropdownId = btn.id.replace("Btn", "Dropdown");
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;

        btn.addEventListener("click", function (e) {
            e.stopPropagation();
            const estaAbierto = dropdown.classList.contains("open");

            document
                .querySelectorAll(".em-dropdown.open")
                .forEach((d) => d.classList.remove("open"));

            if (!estaAbierto) {
                dropdown.classList.add("open");
                btn.setAttribute("aria-expanded", "true");
            } else {
                btn.setAttribute("aria-expanded", "false");
            }
        });
    });

    document.addEventListener("click", function (e) {
        if (
            !e.target.closest(".em-navbar-user") &&
            !e.target.closest(".em-nav-action-wrap")
        ) {
            document
                .querySelectorAll(".em-dropdown.open")
                .forEach((d) => d.classList.remove("open"));
        }
    });

    // ── Modales genéricos (data-open-modal / data-close-modal) ──
    document.querySelectorAll("[data-open-modal]").forEach((btn) => {
        btn.addEventListener("click", function () {
            const modalId = this.dataset.openModal;
            document.getElementById(modalId)?.classList.add("open");
        });
    });

    document.querySelectorAll("[data-close-modal]").forEach((btn) => {
        btn.addEventListener("click", function () {
            const modalId = this.dataset.closeModal;
            document.getElementById(modalId)?.classList.remove("open");
        });
    });

    document.querySelectorAll(".em-modal-overlay").forEach((overlay) => {
        overlay.addEventListener("click", function (e) {
            if (e.target === this) this.classList.remove("open");
        });
    });

    // ── Cerrar alerts (success/error/warning) ────────
    document.querySelectorAll('[data-em-dismiss="alert"]').forEach((btn) => {
        btn.addEventListener("click", function () {
            this.closest(".alert")?.remove();
        });
    });

    // ════════════════════════════════════════════════
    // GRÁFICOS DEL DASHBOARD (solo si existen en la página)
    // ════════════════════════════════════════════════

    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;
    const colorTexto = "#9098a3";
    const paleta = ["#d32f2f", "#1f2125", "#0369a1", "#e65100", "#2e7d32"];

    let chartVentas = null;
    let chartTop = null;
    let rangoActual = "7dias";

    function cargarDatos(params = {}) {
        const url = new URL(
            "/api/dashboard/grafico-datos",
            window.location.origin,
        );
        url.searchParams.set("rango", params.rango ?? rangoActual);
        if (params.desde) url.searchParams.set("desde", params.desde);
        if (params.hasta) url.searchParams.set("hasta", params.hasta);

        fetch(url.toString(), { headers: { "X-CSRF-TOKEN": csrfToken } })
            .then((r) => r.json())
            .then((data) => {
                renderVentas(
                    data.ventas.map((v) => v.fecha),
                    data.ventas.map((v) => v.total),
                );
                renderTop(
                    data.topProductos.map((p) => p.nombre),
                    data.topProductos.map((p) => p.total),
                );
                renderCategorias(data.ventasPorCategoria ?? []);
            });
    }

    const crosshairPlugin = {
        id: "crosshair",
        afterDraw(chart) {
            if (chart.tooltip?._active?.length) {
                const x = chart.tooltip._active[0].element.x;
                const { top, bottom } = chart.chartArea;
                const ctx = chart.ctx;
                ctx.save();
                ctx.beginPath();
                ctx.setLineDash([4, 4]);
                ctx.moveTo(x, top);
                ctx.lineTo(x, bottom);
                ctx.lineWidth = 1;
                ctx.strokeStyle = "rgba(211, 47, 47, 0.4)";
                ctx.stroke();
                ctx.restore();
            }
        },
    };

    function renderVentas(labels, totales) {
        const canvas = document.getElementById("chartVentas7Dias");
        const tooltipEl = document.getElementById("ventasTooltip");
        if (!canvas) return;

        const ctx = canvas.getContext("2d");
        const gradiente = ctx.createLinearGradient(0, 0, 0, 220);
        gradiente.addColorStop(0, "rgba(211, 47, 47, 0.30)");
        gradiente.addColorStop(1, "rgba(211, 47, 47, 0.01)");

        if (chartVentas) chartVentas.destroy();

        chartVentas = new Chart(canvas, {
            type: "line",
            data: {
                labels,
                datasets: [
                    {
                        data: totales,
                        borderColor: "#d32f2f",
                        backgroundColor: gradiente,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: "#ffffff",
                        pointHoverBackgroundColor: "#ffffff",
                        pointBorderColor: "#d32f2f",
                        pointBorderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                interaction: { intersect: false, mode: "index" },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: false,
                        external(context) {
                            const { chart, tooltip } = context;
                            if (!tooltip.opacity) {
                                if (tooltipEl) tooltipEl.style.opacity = 0;
                                return;
                            }
                            const point = tooltip.dataPoints?.[0];
                            if (!point || !tooltipEl) return;

                            tooltipEl.innerHTML = `
                                <div class="tt-title">${point.label}</div>
                                <div class="tt-value">RD$ ${Number(point.parsed.y).toLocaleString("es-DO", { minimumFractionDigits: 2 })}</div>
                            `;

                            const { offsetLeft, offsetTop } = chart.canvas;
                            tooltipEl.style.opacity = 1;
                            tooltipEl.style.left =
                                offsetLeft + tooltip.caretX + 12 + "px";
                            tooltipEl.style.top =
                                offsetTop + tooltip.caretY - 40 + "px";
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: colorTexto, font: { size: 11 } },
                    },
                    y: {
                        grid: { color: "rgba(0,0,0,0.05)" },
                        ticks: {
                            color: colorTexto,
                            font: { size: 11 },
                            callback: (val) =>
                                "RD$ " + val.toLocaleString("es-DO"),
                        },
                    },
                },
            },
            plugins: [crosshairPlugin],
        });
    }

    function renderTop(labels, totales) {
        const canvas = document.getElementById("chartTopProductos");
        const legendEl = document.getElementById("donutLegend");
        const totalEl = document.getElementById("donutTotal");
        if (!canvas) return;

        const total = totales.reduce((a, b) => a + b, 0);
        if (totalEl) totalEl.textContent = total;

        const colores = totales.map((_, i) => paleta[i % paleta.length]);

        if (chartTop) chartTop.destroy();

        chartTop = new Chart(canvas, {
            type: "doughnut",
            data: {
                labels,
                datasets: [
                    {
                        data: totales,
                        backgroundColor: colores,
                        borderWidth: 3,
                        borderColor: "#ffffff",
                        hoverOffset: 6,
                    },
                ],
            },
            options: {
                cutout: "72%",
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "#1f2125",
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: { label: (ctx) => ctx.parsed + " unidades" },
                    },
                },
            },
        });

        if (legendEl) {
            legendEl.innerHTML = labels
                .map((nombre, i) => {
                    const pct =
                        total > 0 ? Math.round((totales[i] / total) * 100) : 0;
                    return `
                    <div class="dash-donut-legend-item">
                        <span class="dash-donut-dot" style="background:${colores[i]}"></span>
                        <span class="nombre">${nombre}</span>
                        <span class="valor">${pct}%</span>
                    </div>
                `;
                })
                .join("");
        }
    }

    function renderCategorias(categorias) {
        const wrap = document.getElementById("categoriaBars");
        if (!wrap) return;

        if (!categorias.length) {
            wrap.innerHTML = `<p style="font-size:12px; color:var(--text-muted); margin:0;">Sin ventas en el período.</p>`;
            return;
        }

        const max = Math.max(...categorias.map((c) => c.total));

        wrap.innerHTML = categorias
            .map((c, i) => {
                const pct = max > 0 ? Math.round((c.total / max) * 100) : 0;
                const color = paleta[i % paleta.length];
                return `
                <div class="dash-cat-bar-row">
                    <div class="dash-cat-bar-top">
                        <span class="dash-cat-bar-nombre">${c.nombre}</span>
                        <span class="dash-cat-bar-valor">RD$ ${c.total.toLocaleString("es-DO", { minimumFractionDigits: 0 })}</span>
                    </div>
                    <div class="dash-cat-bar-track">
                        <div class="dash-cat-bar-fill" style="width:${pct}%; background:${color};"></div>
                    </div>
                </div>
            `;
            })
            .join("");
    }

    function bindFiltrosGrafico() {
        document.querySelectorAll(".em-filtro[data-rango]").forEach((btn) => {
            btn.addEventListener("click", function () {
                const rango = this.dataset.rango;

                document
                    .querySelectorAll(".em-filtro[data-rango]")
                    .forEach((b) => b.classList.remove("active"));
                this.classList.add("active");

                const box = document.getElementById("rangoPersonalizadoBox");
                if (rango === "personalizado") {
                    if (box) box.style.display = "flex";
                    return;
                }
                if (box) box.style.display = "none";
                rangoActual = rango;
                cargarDatos({ rango });
            });
        });

        document
            .getElementById("btnAplicarRango")
            ?.addEventListener("click", function () {
                const desde = document.getElementById("fechaDesde")?.value;
                const hasta = document.getElementById("fechaHasta")?.value;
                if (!desde || !hasta) {
                    alert("Selecciona ambas fechas.");
                    return;
                }
                rangoActual = "personalizado";
                cargarDatos({ rango: "personalizado", desde, hasta });
            });
    }

    if (document.getElementById("chartVentas7Dias")) {
        bindFiltrosGrafico();
        cargarDatos({ rango: "7dias" });
    }
});
