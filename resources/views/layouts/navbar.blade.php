<nav class="em-navbar">

    {{-- ── Left: toggle + breadcrumb ──────────────────── --}}
    <div class="em-navbar__left">

        <button class="em-sidebar-toggle" id="sidebarToggle" title="Colapsar menú" aria-label="Toggle sidebar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        <nav class="em-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Inicio</a>
            @hasSection('page_title')
                <span class="em-breadcrumb__sep">›</span>
                @if(View::hasSection('page_section'))
                    <a href="#">@yield('page_section')</a>
                    <span class="em-breadcrumb__sep">›</span>
                @endif
                <span class="em-breadcrumb__current">@yield('page_title')</span>
            @endif
        </nav>

    </div>

    {{-- ── Right: actions + user ───────────────────────── --}}
    <div class="em-navbar__right">

        {{-- Alertas de stock bajo --}}
        <a href="#" class="em-nav-action" title="Alertas de inventario">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            {{-- Dot activo si hay alertas --}}
            @php $stockAlertas = 0; /* TODO: contar desde DB */ @endphp
            @if($stockAlertas > 0)
                <span class="em-nav-action__dot"></span>
            @endif
        </a>

        {{-- Búsqueda global --}}
        <a href="#" class="em-nav-action" title="Búsqueda">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </a>

        <div class="em-navbar__divider"></div>

        {{-- User dropdown --}}
        <button class="em-navbar-user" id="navbarUserBtn" type="button" aria-expanded="false">
            <div class="em-navbar-user__avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <span class="em-navbar-user__name d-none d-md-inline">
                {{ Auth::user()->name ?? 'Usuario' }}
            </span>
            <svg class="em-navbar-user__caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"/>
            </svg>

            <div class="em-dropdown" id="navbarUserDropdown">
                <a href="#" class="em-dropdown__item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Mi perfil
                </a>
                <a href="#" class="em-dropdown__item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
                    </svg>
                    Configuración
                </a>
                <div class="em-dropdown__sep"></div>
                <button class="em-dropdown__item em-dropdown__item--danger"
                        onclick="event.preventDefault(); document.getElementById('navbar-logout-form').submit();">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Cerrar sesión
                </button>
            </div>
        </button>

        <form id="navbar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

    </div>

</nav>
