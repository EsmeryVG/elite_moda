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

    {{-- Tom Select --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    {{-- Elite Moda CSS --}}
    @vite(['resources/css/dashboard.css'])

    {{-- CSS específico de cada vista --}}
    @stack('styles')
</head>

<body>

    {{-- Mobile overlay --}}
    <div class="em-overlay" id="emOverlay"></div>

    {{-- Layout principal --}}
    <div class="em-layout" id="emLayout">

        @include('layouts.sidebar')

        <div class="em-main-wrapper">

            @include('layouts.navbar')

            <main class="em-main">

                @hasSection('page_title')
                    <div class="em-page-header">
                        <h1 class="em-page-title">@yield('page_title')</h1>
                        @hasSection('page_subtitle')
                            <p class="em-page-subtitle">@yield('page_subtitle')</p>
                        @endif
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible rounded-3 mb-4" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i>
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close" data-em-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible rounded-3 mb-4" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            {{ session('error') }}
                        </div>
                        <button type="button" class="btn-close" data-em-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible rounded-3 mb-4" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ session('warning') }}
                        </div>
                        <button type="button" class="btn-close" data-em-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-x-circle-fill"></i>
                            <strong>Por favor corrige los siguientes errores:</strong>
                        </div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')

            </main>

        </div>

    </div>

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Elite Moda JS --}}
    @vite(['resources/js/dashboard.js'])

    {{-- JS específico de cada vista --}}
    @stack('scripts')

</body>

</html>
