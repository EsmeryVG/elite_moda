/* =====================================================
   Comprobantes Fiscales — JS del módulo
   ===================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // Auto-uppercase en el prefijo NCF
    const prefijoInput = document.querySelector('input[name="prefijo_ncf"]');
    prefijoInput?.addEventListener('input', function () {
        this.value = this.value.toUpperCase();
    });
});