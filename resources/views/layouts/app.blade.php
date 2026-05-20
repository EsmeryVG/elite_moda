<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page_title', 'Dashboard') — Elite Moda</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Elite Moda Dashboard CSS --}}
    @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])

    @stack('styles')
</head>
<body>

{{-- ── Mobile overlay ────────────────────────────────── --}}
<div class="em-overlay" id="emOverlay"></div>

{{-- ── Layout principal ───────────────────────────────── --}}
<div class="em-layout" id="emLayout">

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    {{-- Main wrapper --}}
    <div class="em-main-wrapper">

        {{-- Navbar --}}
        @include('layouts.navbar')

        {{-- Contenido principal --}}
        <main class="em-main">

            {{-- Page header --}}
            @hasSection('page_title')
            <div class="em-page-header">
                <h1 class="em-page-title">@yield('page_title')</h1>
                @hasSection('page_subtitle')
                    <p class="em-page-subtitle">@yield('page_subtitle')</p>
                @endif
            </div>
            @endif

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i>
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-em-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-em-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ session('warning') }}
                    </div>
                    <button type="button" class="btn-close" data-em-dismiss="alert"></button>
                </div>
            @endif

            {{-- Errores de validación globales (si los hay) --}}
            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-x-circle-fill"></i>
                        <strong>Por favor corrige los siguientes errores:</strong>
                    </div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── Contenido de cada vista ───────────────── --}}
            @yield('content')

        </main>

    </div>{{-- /em-main-wrapper --}}

</div>{{-- /em-layout --}}

{{-- Bootstrap 5 JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Elite Moda Dashboard JS --}}
@vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])

@stack('scripts')

</body>
</html>
