<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>

    <!-- Bootswatch + Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/materia/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f5f7fb;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #1f3c88 0%, #0f2557 100%);
        }

        .sidebar .brand {
            font-weight: 700;
            font-size: 1.2rem;
            letter-spacing: 0.4px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.88);
            border-radius: 12px;
            padding: 0.85rem 1rem;
            margin-bottom: 0.35rem;
            transition: 0.2s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.14);
            color: #fff;
        }

        .content-wrapper {
            min-height: 100vh;
        }

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
        }

        .page-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(31, 60, 136, 0.08);
        }

        .table thead th {
            border-bottom-width: 1px;
            white-space: nowrap;
        }

        .btn {
            border-radius: 10px;
        }

        .form-control,
        .form-select,
        .form-control:focus,
        .form-select:focus {
            border-radius: 10px;
            box-shadow: none;
        }

        .badge-soft {
            background-color: #eef3ff;
            color: #1f3c88;
            border: 1px solid #dce6ff;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <aside class="sidebar text-white p-3 p-lg-4">
        <div class="brand mb-4">
            <i class="bi bi-bag-heart-fill me-2"></i> Elite Moda
        </div>

            <nav class="nav flex-column">
            <a href="{{ route('home') }}"
            class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill me-2"></i> Inicio
            </a>

            <a href="{{ route('categorias.index') }}"
            class="nav-link {{ request()->is('categorias*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill me-2"></i> Categorías
            </a>

            <a href="{{ route('productos.index') }}"
            class="nav-link {{ request()->is('productos*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill me-2"></i> Productos
            </a>

            <a href="{{ route('variantes.index') }}"
            class="nav-link {{ request()->is('variantes*') ? 'active' : '' }}">
                <i class="bi bi-palette-fill me-2"></i> Variantes
            </a>
        </nav>
    </aside>

    <main class="flex-grow-1 content-wrapper">
        <div class="topbar px-4 py-3 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">@yield('page_title', 'Panel de gestión')</h4>
                <small class="text-muted">@yield('page_subtitle', 'Administra tu catálogo de forma clara y rápida')</small>
            </div>
        </div>

        <div class="p-4">
            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>