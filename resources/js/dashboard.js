/* =====================================================
   ELITE MODA — Dashboard JS
   ===================================================== */

(function () {
    'use strict';

    const EM = {

        // ── Estado ──────────────────────────────────────
        state: {
            sidebarCollapsed: false,
            mobileSidebarOpen: false,
        },

        // ── Init ────────────────────────────────────────
        init() {
            this.restoreSidebarState();
            this.bindToggle();
            this.bindMobileToggle();
            this.bindOverlay();
            this.bindDropdowns();
            this.bindActiveNav();
            this.bindAlertDismiss();
            this.handleResize();
        },

        // ── Sidebar collapse (desktop) ──────────────────
        restoreSidebarState() {
            const collapsed = localStorage.getItem('em_sidebar_collapsed') === 'true';
            if (collapsed) {
                this.state.sidebarCollapsed = true;
                this.applyCollapsed(true, false);
            }
        },

        bindToggle() {
            const btn = document.getElementById('sidebarToggle');
            if (!btn) return;

            btn.addEventListener('click', () => {
                if (window.innerWidth <= 1024) {
                    this.toggleMobileSidebar();
                } else {
                    this.toggleSidebar();
                }
            });
        },

        toggleSidebar() {
            this.state.sidebarCollapsed = !this.state.sidebarCollapsed;
            localStorage.setItem('em_sidebar_collapsed', this.state.sidebarCollapsed);
            this.applyCollapsed(this.state.sidebarCollapsed, true);
        },

        applyCollapsed(collapsed, animate) {
            const layout  = document.getElementById('emLayout');
            const sidebar = document.getElementById('emSidebar');
            if (!layout || !sidebar) return;

            if (!animate) {
                layout.style.transition  = 'none';
                sidebar.style.transition = 'none';
                requestAnimationFrame(() => {
                    layout.style.transition  = '';
                    sidebar.style.transition = '';
                });
            }

            layout.classList.toggle('sidebar-collapsed', collapsed);
            sidebar.classList.toggle('collapsed', collapsed);
        },

        // ── Sidebar mobile ──────────────────────────────
        bindMobileToggle() {
            // El mismo botón del bindToggle ya lo maneja
        },

        toggleMobileSidebar() {
            this.state.mobileSidebarOpen = !this.state.mobileSidebarOpen;
            const sidebar = document.getElementById('emSidebar');
            const overlay = document.getElementById('emOverlay');
            if (!sidebar || !overlay) return;

            sidebar.classList.toggle('mobile-open', this.state.mobileSidebarOpen);
            overlay.classList.toggle('active', this.state.mobileSidebarOpen);
            document.body.style.overflow = this.state.mobileSidebarOpen ? 'hidden' : '';
        },

        bindOverlay() {
            const overlay = document.getElementById('emOverlay');
            if (!overlay) return;
            overlay.addEventListener('click', () => {
                this.state.mobileSidebarOpen = true;
                this.toggleMobileSidebar();
            });
        },

        // ── Dropdowns ───────────────────────────────────
        bindDropdowns() {
            // Navbar user dropdown
            const userBtn  = document.getElementById('navbarUserBtn');
            const dropdown = document.getElementById('navbarUserDropdown');
            if (userBtn && dropdown) {
                userBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const open = dropdown.classList.toggle('open');
                    userBtn.classList.toggle('open', open);
                });
            }

            // Cerrar al hacer clic fuera
            document.addEventListener('click', () => {
                document.querySelectorAll('.em-dropdown.open').forEach(d => {
                    d.classList.remove('open');
                });
                document.querySelectorAll('.em-navbar-user.open').forEach(b => {
                    b.classList.remove('open');
                });
            });

            // Evitar que un clic dentro del dropdown lo cierre
            document.querySelectorAll('.em-dropdown').forEach(d => {
                d.addEventListener('click', e => e.stopPropagation());
            });
        },

        // ── Nav activo automático ───────────────────────
        bindActiveNav() {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.em-nav-link').forEach(link => {
                const href = link.getAttribute('href');
                if (!href || href === '#') return;

                // Marcar activo si la ruta empieza con el href del link
                const linkPath = new URL(href, window.location.origin).pathname;
                if (currentPath === linkPath || currentPath.startsWith(linkPath + '/')) {
                    link.classList.add('active');

                    // Si está en un submenú, expandir el padre
                    const parentGroup = link.closest('.em-nav-group');
                    if (parentGroup) {
                        parentGroup.classList.add('open');
                    }
                }
            });
        },

        // ── Alerts auto-dismiss ─────────────────────────
        bindAlertDismiss() {
            // Auto-dismiss de alertas de flash después de 4s
            document.querySelectorAll('.alert-dismissible, .em-alert').forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease, max-height 0.4s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-6px)';
                    setTimeout(() => alert.remove(), 400);
                }, 4000);
            });

            // Close buttons
            document.querySelectorAll('[data-em-dismiss="alert"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const alert = btn.closest('.em-alert, .alert');
                    if (alert) alert.remove();
                });
            });
        },

        // ── Resize handler ──────────────────────────────
        handleResize() {
            window.addEventListener('resize', () => {
                if (window.innerWidth > 1024) {
                    // Cerrar mobile sidebar al volver a desktop
                    if (this.state.mobileSidebarOpen) {
                        const sidebar = document.getElementById('emSidebar');
                        const overlay = document.getElementById('emOverlay');
                        sidebar?.classList.remove('mobile-open');
                        overlay?.classList.remove('active');
                        document.body.style.overflow = '';
                        this.state.mobileSidebarOpen = false;
                    }
                }
            });
        },
    };

    // Iniciar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => EM.init());
    } else {
        EM.init();
    }

    // Exponer para uso externo si es necesario
    window.EliteModa = EM;

})();
