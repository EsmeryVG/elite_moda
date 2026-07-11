/* =====================================================
   Dashboard — Gráficos
   ===================================================== */

document.addEventListener('DOMContentLoaded', () => {

    // ── Colores base del sistema ──────────────────────
    const colorPrimario  = '#1f2125';
    const colorSecundario = 'rgba(31,33,37,0.15)';
    const colorTexto     = '#888';

    // ── Gráfico: Ventas últimos 7 días ────────────────
    const ctx7 = document.getElementById('chartVentas7Dias');
    if (ctx7 && typeof ventasLabels !== 'undefined') {
        new Chart(ctx7, {
            type: 'bar',
            data: {
                labels: ventasLabels,
                datasets: [{
                    label: 'Ventas (RD$)',
                    data: ventasTotales,
                    backgroundColor: colorSecundario,
                    borderColor: colorPrimario,
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'RD$ ' + ctx.parsed.y.toLocaleString('es-DO', {
                                minimumFractionDigits: 2
                            })
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: colorTexto, font: { size: 11 } }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            color: colorTexto,
                            font: { size: 11 },
                            callback: val => 'RD$ ' + val.toLocaleString('es-DO')
                        }
                    }
                }
            }
        });
    }

    // ── Gráfico: Top 5 productos del mes ─────────────
    const ctxTop = document.getElementById('chartTopProductos');
    if (ctxTop && typeof topProductosLabels !== 'undefined') {
        new Chart(ctxTop, {
            type: 'bar',
            data: {
                labels: topProductosLabels,
                datasets: [{
                    label: 'Unidades vendidas',
                    data: topProductosTotales,
                    backgroundColor: colorSecundario,
                    borderColor: colorPrimario,
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.x + ' unidades'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { color: colorTexto, font: { size: 11 } }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            color: colorTexto,
                            font: { size: 11 },
                            callback: function(val) {
                                const label = this.getLabelForValue(val);
                                return label.length > 20 ? label.substring(0, 20) + '...' : label;
                            }
                        }
                    }
                }
            }
        });
    }

});