<nav class="em-navbar">
    {{-- ── Left: toggle + breadcrumb ──────────────────── --}}
    <div class="em-navbar__left">
        <button class="em-sidebar-toggle" id="sidebarToggle" title="Colapsar menú" aria-label="Toggle sidebar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>
        <nav class="em-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Inicio</a>
            @hasSection('page_title')
                <span class="em-breadcrumb__sep">›</span>
                @if (View::hasSection('page_section'))
                    <a href="#">@yield('page_section')</a>
                    <span class="em-breadcrumb__sep">›</span>
                @endif
                <span class="em-breadcrumb__current">@yield('page_title')</span>
            @endif
        </nav>
    </div>

    {{-- ── Right: actions + user ───────────────────────── --}}
    <div class="em-navbar__right">

        {{-- Notificaciones --}}
        <div class="em-nav-action-wrap">
            <button type="button" class="em-nav-action" id="navbarAlertasBtn" title="Alertas">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
                @if (count($alertasNavbar ?? []) > 0)
                    <span class="em-nav-action__dot">{{ count($alertasNavbar) }}</span>
                @endif
            </button>

            <div class="em-dropdown em-dropdown--alertas" id="navbarAlertasDropdown">
                @forelse($alertasNavbar ?? [] as $alerta)
                    <a href="{{ $alerta['url'] }}"
                        class="em-dropdown__item em-dropdown__item--alerta em-dropdown__item--{{ $alerta['tipo'] }}">
                        <i class="bi bi-{{ $alerta['icono'] }}"></i>
                        <span>{{ $alerta['texto'] }}</span>
                    </a>
                @empty
                    <div class="em-dropdown__empty">
                        <i class="bi bi-check-circle"></i>
                        Todo en orden, sin alertas.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="em-navbar__divider"></div>

        {{-- User dropdown --}}
        {{-- User dropdown --}}
        <div class="em-navbar-user-wrap" style="position:relative;">
            <div class="em-navbar-user" id="navbarUserBtn" role="button" tabindex="0" aria-expanded="false">
                <div class="em-navbar-user__avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <span class="em-navbar-user__name d-none d-md-inline">
                    {{ Auth::user()->name ?? 'Usuario' }}
                </span>
                <svg class="em-navbar-user__caret" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <polyline points="6 9 12 15 18 9" />
                </svg>
            </div>
            <div class="em-dropdown" id="navbarUserDropdown">
                <button type="button" class="em-dropdown__item" data-open-modal="modalCambiarPassword">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    Cambiar contraseña
                </button>
                @if (Auth::user()->esAdministrador())
                    <a href="{{ route('configuraciones.index') }}" class="em-dropdown__item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14" />
                        </svg>
                        Configuración
                    </a>
                @endif
                <div class="em-dropdown__sep"></div>
                <button class="em-dropdown__item em-dropdown__item--danger"
                    onclick="event.preventDefault(); document.getElementById('navbar-logout-form').submit();">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    Cerrar sesión
                </button>
            </div>
        </div>
        <form id="navbar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</nav>

{{-- ── Modal: Cambiar contraseña ────────────────────── --}}
<div class="em-modal-overlay" id="modalCambiarPassword">
    <div class="em-modal">
        <h6 class="em-modal__title">Cambiar contraseña</h6>
        <form action="{{ route('perfil.password.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Contraseña actual</label>
                <input type="password" name="password_actual"
                    class="form-control @error('password_actual') is-invalid @enderror"
                    autocomplete="current-password">
                @error('password_actual')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Nueva contraseña</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-0">
                <label class="form-label">Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation" class="form-control"
                    autocomplete="new-password">
            </div>
            <div class="em-modal__actions">
                <button type="button" class="btn btn-secondary" data-close-modal="modalCambiarPassword">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Actualizar contraseña
                </button>
            </div>
        </form>
    </div>
</div>
