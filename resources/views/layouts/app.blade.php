<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>EconomyManager - @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar global -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">EconomyManager</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto">
                    <!-- Opción directa: Catálogo -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('catalogo.index') }}">Catálogo</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('balance.index') }}">Balance General</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('estado-resultados.index') }}">Estado de Resultados</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('ratios.index') }}">Ratios</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('graficos.index') }}">Grafico de Ratios</a>
                    </li>

                    <!-- Dropdown: Comparaciones -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="comparacionesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Comparaciones
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="comparacionesDropdown">
                            <li><a class="dropdown-item" href="{{ route('comparacion.sector.index') }}">Comparacion por Sector</a></li>
                            <li><a class="dropdown-item" href="{{ route('comparacion.promedio.index') }}">Comparacion por Promedio</a></li>
                            <li><a class="dropdown-item" href="{{ route('comparacion.index') }}">Comparacion Ratios</a></li>
                            
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('proyeccion.index') }}">Proyeccion de Ventas</a>
                    </li>

                    <!-- Dropdown: Otros -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="otrosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Otros
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="otrosDropdown">
                            <li><a class="dropdown-item" href="{{ route('sector.index') }}">Sectores</a></li>
                            <li><a class="dropdown-item" href="{{ route('empresa.index') }}">Empresas</a></li>
                            
                        </ul>
                    </li>
                </ul>

                <!-- Usuario -->
                <div class="dropdown ms-auto">
                    <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ session('usuario_nombre') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Información del usuario</h6></li>
                        <li><span class="dropdown-item-text">Nombre: {{ session('usuario_nombre') }}</span></li>
                        <li>
                            <span class="dropdown-item-text">
                                Roles:
                                <ul class="mb-0">
                                    @foreach (session('usuario_rol') as $rol)
                                        <li>{{ $rol }}</li>
                                    @endforeach
                                </ul>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{ route('logout') }}">Cerrar sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>



    <!-- Contenido dinámico -->
    <div class="container mt-5">
        @yield('content')
    </div>

    <!-- jQuery (necesario para los gráficos) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts adicionales -->
    @yield('scripts')
</body>
</html>