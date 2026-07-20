<aside class="em-sidebar" id="emSidebar">
    {{-- ── Brand ──────────────────────────────────────── --}}
    <a href="{{ route('home') }}" class="em-sidebar__brand">
        <img src="{{ asset('images/logo-elite-moda.png') }}" alt="Elite Moda" class="em-sidebar__logo"
            onerror="this.style.display='none'">
        <div class="em-sidebar__brand-text">
            <span class="em-sidebar__brand-name">elite</span>
            <span class="em-sidebar__brand-sub">moda</span>
        </div>
    </a>
    {{-- ── Navigation ──────────────────────────────────── --}}
    <nav class="em-sidebar__nav">
        {{-- Principal --}}
        <div class="em-nav-section">
            <div class="em-nav-section__label">Principal</div>
            <div class="em-nav-item">
                <a href="{{ route('home') }}" class="em-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    <span class="em-nav-label">Dashboard</span>
                </a>
                <div class="em-tooltip">Dashboard</div>
            </div>
        </div>

        {{-- Catálogo --}}
        @permiso('productos.ver')
            <div class="em-nav-section">
                <div class="em-nav-section__label">Catálogo</div>
                <div class="em-nav-item">
                    <a href="{{ route('productos.index') }}"
                        class="em-nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                            <line x1="7" y1="7" x2="7.01" y2="7" />
                        </svg>
                        <span class="em-nav-label">Productos</span>
                    </a>
                    <div class="em-tooltip">Productos</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('categorias.index') }}"
                        class="em-nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <rect x="3" y="3" width="7" height="7" />
                            <rect x="14" y="3" width="7" height="7" />
                            <rect x="14" y="14" width="7" height="7" />
                            <rect x="3" y="14" width="7" height="7" />
                        </svg>
                        <span class="em-nav-label">Categorías</span>
                    </a>
                    <div class="em-tooltip">Categorías</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('atributos.index') }}"
                        class="em-nav-link {{ request()->routeIs('atributos.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <circle cx="12" cy="12" r="3" />
                            <path
                                d="M12 2v3M12 19v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M2 12h3M19 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12" />
                        </svg>
                        <span class="em-nav-label">Atributos</span>
                    </a>
                    <div class="em-tooltip">Atributos</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('descuentos.index') }}"
                        class="em-nav-link {{ request()->routeIs('descuentos.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <circle cx="9" cy="21" r="1" />
                            <circle cx="20" cy="21" r="1" />
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                        </svg>
                        <span class="em-nav-label">Descuentos</span>
                    </a>
                    <div class="em-tooltip">Descuentos</div>
                </div>
            </div>
        @endpermiso

        {{-- Inventario --}}
        @permiso('inventario.ver')
            <div class="em-nav-section">
                <div class="em-nav-section__label">Inventario</div>
                <div class="em-nav-item">
                    <a href="{{ route('stock.index') }}"
                        class="em-nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path
                                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                            <line x1="12" y1="22.08" x2="12" y2="12" />
                        </svg>
                        <span class="em-nav-label">Stock</span>
                    </a>
                    <div class="em-tooltip">Stock</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('ajustes.index') }}"
                        class="em-nav-link {{ request()->routeIs('ajustes.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                        <span class="em-nav-label">Ajustes</span>
                    </a>
                    <div class="em-tooltip">Ajustes</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('movimientos.index') }}"
                        class="em-nav-link {{ request()->routeIs('movimientos.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                        </svg>
                        <span class="em-nav-label">Movimientos</span>
                    </a>
                    <div class="em-tooltip">Movimientos</div>
                </div>
                @permiso('almacenes.ver')
                    <div class="em-nav-item">
                        <a href="{{ route('almacenes.index') }}"
                            class="em-nav-link {{ request()->routeIs('almacenes.*') ? 'active' : '' }}">
                            <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                            </svg>
                            <span class="em-nav-label">Almacenes</span>
                        </a>
                        <div class="em-tooltip">Almacenes</div>
                    </div>
                @endpermiso
            </div>
        @endpermiso

        {{-- Ventas --}}
        <div class="em-nav-section">
            <div class="em-nav-section__label">Ventas</div>
            @permiso('ventas.vender')
                <div class="em-nav-item">
                    <a href="{{ route('ventas.create') }}"
                        class="em-nav-link {{ request()->routeIs('ventas.create') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <rect x="2" y="3" width="20" height="14" rx="2" />
                            <line x1="8" y1="21" x2="16" y2="21" />
                            <line x1="12" y1="17" x2="12" y2="21" />
                        </svg>
                        <span class="em-nav-label">Nueva Venta (POS)</span>
                    </a>
                    <div class="em-tooltip">Nueva Venta</div>
                </div>
            @endpermiso
            @permiso('ventas.ver')
                <div class="em-nav-item">
                    <a href="{{ route('ventas.index') }}"
                        class="em-nav-link {{ request()->routeIs('ventas.index') || request()->routeIs('ventas.show') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                        <span class="em-nav-label">Historial Ventas</span>
                    </a>
                    <div class="em-tooltip">Historial</div>
                </div>
            @endpermiso
            @permiso('devoluciones.ver')
                <div class="em-nav-item">
                    <a href="{{ route('devoluciones.index') }}"
                        class="em-nav-link {{ request()->routeIs('devoluciones.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <path d="M9 22V12h6v10" />
                            <path d="M1 4l11 7 11-7" />
                        </svg>
                        <span class="em-nav-label">Devoluciones</span>
                    </a>
                    <div class="em-tooltip">Devoluciones</div>
                </div>
            @endpermiso
            @permiso('notas_credito.ver')
                <div class="em-nav-item">
                    <a href="{{ route('notas_credito.index') }}"
                        class="em-nav-link {{ request()->routeIs('notas_credito.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <line x1="12" y1="1" x2="12" y2="23" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                        <span class="em-nav-label">Notas de Crédito</span>
                    </a>
                    <div class="em-tooltip">Notas de Crédito</div>
                </div>
            @endpermiso
        </div>

        {{-- Clientes --}}
        @permiso('clientes.ver')
            <div class="em-nav-section">
                <div class="em-nav-section__label">Clientes</div>
                <div class="em-nav-item">
                    <a href="{{ route('clientes.index') }}"
                        class="em-nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <span class="em-nav-label">Clientes</span>
                    </a>
                    <div class="em-tooltip">Clientes</div>
                </div>
                @permiso('cxc.ver')
                    <div class="em-nav-item">
                        <a href="{{ route('cuentas_por_cobrar.index') }}"
                            class="em-nav-link {{ request()->routeIs('cuentas_por_cobrar.*') ? 'active' : '' }}">
                            <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                                <line x1="1" y1="10" x2="23" y2="10" />
                            </svg>
                            <span class="em-nav-label">Cuentas por Cobrar</span>
                        </a>
                        <div class="em-tooltip">Cuentas x Cobrar</div>
                    </div>
                @endpermiso
                <div class="em-nav-item">
                    <a href="{{ route('grupo_clientes.index') }}"
                        class="em-nav-link {{ request()->routeIs('grupo_clientes.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <line x1="23" y1="11" x2="17" y2="11" />
                        </svg>
                        <span class="em-nav-label">Grupos de Cliente</span>
                    </a>
                    <div class="em-tooltip">Grupos</div>
                </div>
            </div>
        @endpermiso

        {{-- Compras --}}
        @permiso('compras.ver')
            <div class="em-nav-section">
                <div class="em-nav-section__label">Compras</div>
                <div class="em-nav-item">
                    <a href="{{ route('ordenes_compra.index') }}"
                        class="em-nav-link {{ request()->routeIs('ordenes_compra.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                        </svg>
                        <span class="em-nav-label">Órdenes de Compra</span>
                    </a>
                    <div class="em-tooltip">Órdenes</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('recepciones.index') }}"
                        class="em-nav-link {{ request()->routeIs('recepciones.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M5 12h14" />
                            <path d="M12 5l7 7-7 7" />
                        </svg>
                        <span class="em-nav-label">Recepciones</span>
                    </a>
                    <div class="em-tooltip">Recepciones</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('proveedores.index') }}"
                        class="em-nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                        </svg>
                        <span class="em-nav-label">Proveedores</span>
                    </a>
                    <div class="em-tooltip">Proveedores</div>
                </div>
            </div>
        @endpermiso

        {{-- Caja --}}
        @permiso('caja.ver')
            <div class="em-nav-section">
                <div class="em-nav-section__label">Caja</div>
                <div class="em-nav-item">
                    <a href="{{ route('cajas.index') }}"
                        class="em-nav-link {{ request()->routeIs('cajas.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M3 10h18M5 6h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z" />
                            <path d="M9 14h6" />
                        </svg>
                        <span class="em-nav-label">Cajas</span>
                    </a>
                    <div class="em-tooltip">Cajas</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('sesiones_caja.index') }}"
                        class="em-nav-link {{ request()->routeIs('sesiones_caja.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                        </svg>
                        <span class="em-nav-label">Sesiones de Caja</span>
                    </a>
                    <div class="em-tooltip">Sesiones</div>
                </div>
                @permiso('caja_chica.ver')
                    <div class="em-nav-item">
                        <a href="{{ route('caja_chica.show') }}"
                            class="em-nav-link {{ request()->routeIs('caja_chica.*') ? 'active' : '' }}">
                            <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                            </svg>
                            <span class="em-nav-label">Caja Chica</span>
                        </a>
                        <div class="em-tooltip">Caja Chica</div>
                    </div>
                @endpermiso
                <div class="em-nav-item">
                    <a href="{{ route('cuadre_diario.index') }}"
                        class="em-nav-link {{ request()->routeIs('cuadre_diario.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <span class="em-nav-label">Cuadre Diario</span>
                    </a>
                    <div class="em-tooltip">Cuadre</div>
                </div>
            </div>
        @endpermiso

        {{-- Gastos --}}
        @permiso('gastos.ver')
            <div class="em-nav-section">
                <div class="em-nav-section__label">Gastos</div>
                <div class="em-nav-item">
                    <a href="{{ route('gastos.index') }}"
                        class="em-nav-link {{ request()->routeIs('gastos.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <line x1="12" y1="1" x2="12" y2="23" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                        <span class="em-nav-label">Gastos</span>
                    </a>
                    <div class="em-tooltip">Gastos</div>
                </div>
            </div>
        @endpermiso

        {{-- Personal --}}
        @permiso('empleados.ver')
            <div class="em-nav-section">
                <div class="em-nav-section__label">Personal</div>
                <div class="em-nav-item">
                    <a href="{{ route('empleados.index') }}"
                        class="em-nav-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <span class="em-nav-label">Empleados</span>
                    </a>
                    <div class="em-tooltip">Empleados</div>
                </div>
                @permiso('nomina.ver')
                    <div class="em-nav-item">
                        <a href="{{ route('nomina.index') }}"
                            class="em-nav-link {{ request()->routeIs('nomina.*') ? 'active' : '' }}">
                            <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <line x1="12" y1="20" x2="12" y2="10" />
                                <line x1="18" y1="20" x2="18" y2="4" />
                                <line x1="6" y1="20" x2="6" y2="16" />
                            </svg>
                            <span class="em-nav-label">Comisiones</span>
                        </a>
                        <div class="em-tooltip">Comisiones</div>
                    </div>
                @endpermiso
            </div>
        @endpermiso

        {{-- Reportes --}}
        @permiso('reportes.ver')
            <div class="em-nav-section">
                <div class="em-nav-section__label">Reportes</div>
                <div class="em-nav-item">
                    <a href="{{ route('reportes.ventas') }}"
                        class="em-nav-link {{ request()->routeIs('reportes.ventas') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <line x1="18" y1="20" x2="18" y2="10" />
                            <line x1="12" y1="20" x2="12" y2="4" />
                            <line x1="6" y1="20" x2="6" y2="14" />
                        </svg>
                        <span class="em-nav-label">Ventas</span>
                    </a>
                    <div class="em-tooltip">Reporte Ventas</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('reportes.inventario') }}"
                        class="em-nav-link {{ request()->routeIs('reportes.inventario') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <path d="M3 9h18M9 21V9" />
                        </svg>
                        <span class="em-nav-label">Inventario</span>
                    </a>
                    <div class="em-tooltip">Reporte Inventario</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('reportes.compras') }}"
                        class="em-nav-link {{ request()->routeIs('reportes.compras') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                        <span class="em-nav-label">Compras</span>
                    </a>
                    <div class="em-tooltip">Reporte Compras</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('reportes.credito') }}"
                        class="em-nav-link {{ request()->routeIs('reportes.credito') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        </svg>
                        <span class="em-nav-label">Crédito y Cobros</span>
                    </a>
                    <div class="em-tooltip">Reporte Crédito</div>
                </div>
            </div>
        @endpermiso

        {{-- Configuración --}}
        @permiso('configuracion.gestionar')
            <div class="em-nav-section">
                <div class="em-nav-section__label">Configuración</div>
                <div class="em-nav-item">
                    <a href="{{ route('configuraciones.index') }}"
                        class="em-nav-link {{ request()->routeIs('configuraciones.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path
                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33
                                                            1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2
                                                            2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65
                                                            0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65
                                                            1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83
                                                            2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .66.39 1.26 1 1.51H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                        </svg>
                        <span class="em-nav-label">Configuración</span>
                    </a>
                    <div class="em-tooltip">Configuración</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('sucursales.index') }}"
                        class="em-nav-link {{ request()->routeIs('sucursales.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                        <span class="em-nav-label">Sucursales</span>
                    </a>
                    <div class="em-tooltip">Sucursales</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('comprobantes.index') }}"
                        class="em-nav-link {{ request()->routeIs('comprobantes.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path
                                d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                        </svg>
                        <span class="em-nav-label">Comprobantes NCF</span>
                    </a>
                    <div class="em-tooltip">NCF</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('tipos_pago.index') }}"
                        class="em-nav-link {{ request()->routeIs('tipos_pago.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                            <line x1="1" y1="10" x2="23" y2="10" />
                        </svg>
                        <span class="em-nav-label">Tipos de Pago</span>
                    </a>
                    <div class="em-tooltip">Tipos de Pago</div>
                </div>
            </div>
        @endpermiso

        @permiso('usuarios.gestionar')
            <div class="em-nav-section">
                <div class="em-nav-section__label">Usuarios y Roles</div>
                <div class="em-nav-item">
                    <a href="{{ route('usuarios.index') }}"
                        class="em-nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <span class="em-nav-label">Usuarios</span>
                    </a>
                    <div class="em-tooltip">Usuarios</div>
                </div>
                <div class="em-nav-item">
                    <a href="{{ route('roles.index') }}"
                        class="em-nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <svg class="em-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14" />
                        </svg>
                        <span class="em-nav-label">Roles</span>
                    </a>
                    <div class="em-tooltip">Roles</div>
                </div>
            </div>
        @endpermiso
    </nav>
    {{-- ── Footer / User ────────────────────────────── --}}
    <div class="em-sidebar__footer">
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();"
            class="em-sidebar__user">
            <div class="em-user-avatar">
                {{ mb_strtoupper(mb_substr(Auth::user()->name ?? 'U', 0, 1), 'UTF-8') }}
            </div>
            <div class="em-user-info">
                <div class="em-user-name">{{ Auth::user()->name ?? 'Usuario' }}</div>
                <div class="em-user-role">{{ Auth::user()->rol->nombre ?? 'Sin rol' }}</div>
            </div>
        </a>
        <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</aside>
