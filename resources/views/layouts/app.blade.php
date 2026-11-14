<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-100 overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EconomyManager - @yield('title', 'Dashboard Financiero')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Alpine.js para interactividad suave de menús (Apertura al clic sostenida) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Carga CSS/JS mediante Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-[#f1f5f9] flex flex-col overflow-x-hidden">

    <!-- Indicador de línea superior corporativa -->
    <div class="h-1 bg-gradient-to-r from-cyan-500 via-blue-600 to-emerald-500"></div>

    <!-- Header / Navbar Principal -->
    <header class="sticky top-0 z-50 w-full border-b border-slate-800 bg-[#0f172a] text-white shadow-xl">
        <div class="w-full px-4 sm:px-6">
            <div class="flex h-16 items-center justify-between gap-4">
                
                <!-- 1. Marca / Logo Unificado -->
                <div class="flex items-center gap-3 flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-extrabold text-base sm:text-lg tracking-tight text-white hover:opacity-95 transition">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-black shadow-md shadow-cyan-500/20 flex-shrink-0">
                            <i class="bi bi-graph-up-arrow text-sm"></i>
                        </div>
                        <span class="tracking-tight font-bold">Economy<span class="text-cyan-400 font-semibold">Manager</span></span>
                    </a>
                </div>

                <!-- 2. Menú de Navegación Desktop -->
                <nav class="hidden lg:flex items-center justify-evenly flex-1 max-w-5xl mx-2 gap-1 xl:gap-2">
                    
                    <a href="{{ route('catalogo.index') }}" 
                       class="px-2.5 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('catalogo.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border border-cyan-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                       Catálogo
                    </a>

                    <a href="{{ route('balance.index') }}" 
                       class="px-2.5 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('balance.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border border-cyan-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                       Balance General
                    </a>

                    <a href="{{ route('estado-resultados.index') }}" 
                       class="px-2.5 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('estado-resultados.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border border-cyan-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                       Estado Resultados
                    </a>

                    <a href="{{ route('ratios.index') }}" 
                       class="px-2.5 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('ratios.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border border-cyan-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                       Ratios
                    </a>

                    <a href="{{ route('graficos.index') }}" 
                       class="px-2.5 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('graficos.*') ? 'bg-cyan-500/10 text-cyan-400 font-semibold border border-cyan-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                       Gráficos
                    </a>

                    <!-- Dropdown: Comparaciones (Sostenido por clic) -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" type="button" class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition whitespace-nowrap">
                            <span class="{{ request()->routeIs('comparacion.*') ? 'text-cyan-400 font-semibold' : '' }}">Comparaciones</span>
                            <i class="bi bi-chevron-down text-xs text-slate-400 transition" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition
                             class="absolute left-0 mt-2 w-48 rounded-xl bg-white p-1.5 shadow-2xl ring-1 ring-black/5 z-50 text-left">
                            <a href="{{ route('comparacion.sector.index') }}" class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-cyan-600 transition">Por Sector</a>
                            <a href="{{ route('comparacion.promedio.index') }}" class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-cyan-600 transition">Por Promedio</a>
                            <a href="{{ route('comparacion.index') }}" class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-cyan-600 transition">Comparación Ratios</a>
                        </div>
                    </div>

                  
                    <!-- Dropdown: Análisis Financiero -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                @click.outside="open = false"
                                type="button"
                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition whitespace-nowrap">
                    
                            <span class="{{ request()->routeIs('analisis.*') ? 'text-cyan-400 font-semibold' : '' }}">
                                Análisis
                            </span>
                    
                            <i class="bi bi-chevron-down text-xs text-slate-400 transition"
                               :class="{ 'rotate-180': open }"></i>
                        </button>
                    
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-52 rounded-xl bg-white p-1.5 shadow-2xl ring-1 ring-black/5 z-50 text-left">
                    
                            <a href="{{ route('analisis.index') }}"
                               class="block px-3 py-2 rounded-lg text-xs font-bold text-slate-900 hover:bg-cyan-50 hover:text-cyan-700 transition">
                                Panel General
                            </a>
                    
                            <div class="my-1 border-t border-slate-100"></div>
                    
                            <a href="{{ route('analisis.historial.vertical') }}"
                               class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-cyan-600 transition">
                                Análisis Vertical
                            </a>
                            
                            <a href="{{ route('analisis.historial.horizontal') }}"
                               class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-cyan-600 transition">
                                Análisis Horizontal
                            </a>
                        </div>
                    </div>


                    <!-- Dropdown: Ajustes -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" type="button" class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition whitespace-nowrap">
                            <span class="{{ request()->routeIs('sector.*') || request()->routeIs('empresa.*') ? 'text-cyan-400 font-semibold' : '' }}">Ajustes</span>
                            <i class="bi bi-chevron-down text-xs text-slate-400 transition" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition
                             class="absolute right-0 mt-2 w-44 rounded-xl bg-white p-1.5 shadow-2xl ring-1 ring-black/5 z-50 text-left">
                            <a href="{{ route('sector.index') }}" class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-cyan-600 transition">Sectores</a>
                            <a href="{{ route('empresa.index') }}" class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-cyan-600 transition">Empresas</a>
                        </div>
                    </div>

                </nav>

                <!-- 3. Controles Derecha: Perfil de Usuario Rediseñado (Sin "Sesión Activa") -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    
                    @php
                        $nombreUsuario = session('usuario_nombre') ?? (Auth::check() ? Auth::user()->name : 'Ana');
                        $rolesUsuario = session('usuario_rol') ?? (Auth::check() ? [Auth::user()->rol ?? 'Administrador'] : ['Administrador']);
                        $inicial = strtoupper(substr($nombreUsuario, 0, 1));
                    @endphp

                    <!-- Dropdown de Usuario con Clic Sostenido -->
                    <div class="relative" x-data="{ open: false }">
                        <!-- Botón Selector -->
                        <button @click="open = !open" @click.outside="open = false" type="button" 
                                class="flex items-center gap-2.5 rounded-xl border border-slate-700/80 bg-slate-800/80 hover:bg-slate-700/90 px-3 py-1.5 text-xs font-medium text-slate-200 transition focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
                            <div class="h-6 w-6 rounded-lg bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 flex items-center justify-center font-bold font-mono text-xs shadow-2xs">
                                {{ $inicial }}
                            </div>
                            <span class="hidden md:inline font-semibold text-white tracking-tight">{{ $nombreUsuario }}</span>
                            <i class="bi bi-chevron-down text-[10px] text-slate-400 transition" :class="{ 'rotate-180': open }"></i>
                        </button>

                        <!-- Menú Desplegable Rediseñado -->
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-64 rounded-2xl bg-white p-3 shadow-2xl border border-slate-200/80 z-50 text-left">
                            
                            <!-- Tarjeta de Usuario Elegante -->
                            <div class="p-3 bg-slate-900 rounded-xl text-white space-y-2 mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-cyan-500 text-slate-900 font-bold font-mono flex items-center justify-center text-sm">
                                        {{ $inicial }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="flex items-center gap-1.5">
                                            <p class="text-xs font-bold truncate text-white">{{ $nombreUsuario }}</p>
                                            <span class="h-2 w-2 rounded-full bg-emerald-400 shrink-0"></span>
                                        </div>
                                        <div class="flex flex-wrap gap-1 mt-0.5">
                                            @foreach ((array) $rolesUsuario as $rol)
                                                <span class="text-[10px] text-cyan-300 font-semibold uppercase tracking-wider">{{ $rol }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Opciones -->
                            <div class="space-y-1">
                                <a href="{{ route('logout') }}" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                    <i class="bi bi-box-arrow-right text-sm"></i>
                                    <span>Cerrar sesión</span>
                                </a>

                                <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Botón Hamburguesa Responsivo -->
                    <button id="btn-menu-mobile" type="button" class="lg:hidden p-1.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
                        <i class="bi bi-list text-xl"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- 4. Menú Desplegable Responsivo para Pantallas Pequeñas -->
        <div id="menu-mobile" class="hidden lg:hidden border-t border-slate-800 bg-[#0b1329] px-4 pt-3 pb-6 space-y-2">
            <a href="{{ route('catalogo.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Catálogo</a>
            <a href="{{ route('balance.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Balance General</a>
            <a href="{{ route('estado-resultados.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Estado Resultados</a>
            <a href="{{ route('ratios.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Ratios</a>
            <a href="{{ route('graficos.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Gráficos</a>

            <div class="pt-2 border-t border-slate-800/60">
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-cyan-400">Comparaciones</p>
                <a href="{{ route('comparacion.sector.index') }}" class="block px-3 py-1.5 text-xs text-slate-300 hover:text-white">Por Sector</a>
                <a href="{{ route('comparacion.promedio.index') }}" class="block px-3 py-1.5 text-xs text-slate-300 hover:text-white">Por Promedio</a>
                <a href="{{ route('comparacion.index') }}" class="block px-3 py-1.5 text-xs text-slate-300 hover:text-white">Comparación Ratios</a>
            </div>

            <div class="pt-2 border-t border-slate-800/60">

             <a href="{{ route('analisis.index') }}"
                class="block px-3 py-1.5 text-xs text-slate-300 hover:text-white">
                 Panel General
             </a>
             
             <a href="{{ route('analisis.historial.vertical') }}">
                 Análisis Vertical
             </a>
             
             <a href="{{ route('analisis.historial.horizontal') }}"
                class="block px-3 py-1.5 text-xs text-slate-300 hover:text-white">
                 Análisis Horizontal
             </a>
            </div>

            <a href="{{ route('proyeccion.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Proyección</a>

            <div class="pt-2 border-t border-slate-800/60">
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-cyan-400">Ajustes</p>
                <a href="{{ route('sector.index') }}" class="block px-3 py-1.5 text-xs text-slate-300 hover:text-white">Sectores</a>
                <a href="{{ route('empresa.index') }}" class="block px-3 py-1.5 text-xs text-slate-300 hover:text-white">Empresas</a>
            </div>
        </div>
    </header>

    <!-- Contenido Dinámico Principal -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.getElementById('btn-menu-mobile').addEventListener('click', function() {
            var menu = document.getElementById('menu-mobile');
            menu.classList.toggle('hidden');
        });
    </script>
    @yield('scripts')
</body>
</html>