<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>EconomyManager — Inicio de Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased selection:bg-cyan-500 selection:text-white">

    <!-- Tarjeta Principal del Login -->
    <div class="w-full max-w-md">
        
        <!-- Logo / Marca Superior -->
        <div class="text-center mb-8 space-y-2">
            <div class="inline-flex items-center justify-center h-14 w-14 rounded-2xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 shadow-lg shadow-cyan-500/5 mb-2">
                <i class="bi bi-graph-up-arrow text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">EconomyManager</h1>
            <p class="text-xs text-slate-400">Sistema Integral de Gestión y Análisis Financiero</p>
        </div>

        <!-- Contenedor del Formulario -->
        <div class="bg-slate-800/80 backdrop-blur-xl rounded-2xl border border-slate-700/60 shadow-2xl overflow-hidden">
            
            <div class="p-8 space-y-6">
                
                <div class="space-y-1">
                    <h2 class="text-lg font-bold text-white tracking-tight">Iniciar Sesión</h2>
                    <p class="text-xs text-slate-400">Ingresa tus credenciales para acceder al panel</p>
                </div>

                <!-- Alertas de Error del Sistema -->
                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs flex items-start gap-3 shadow-sm">
                        <i class="bi bi-exclamation-octagon-fill text-rose-400 text-base mt-0.5 shrink-0"></i>
                        <div class="space-y-1">
                            <span class="font-bold">Error de Autenticación</span>
                            <p class="text-rose-300/90 leading-relaxed">
                                {{ $errors->first('login') ?? $errors->first() }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Formulario POST -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Correo Electrónico -->
                    <div class="space-y-1.5">
                        <label for="correo" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
                            Correo Electrónico
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                                <i class="bi bi-envelope text-sm"></i>
                            </span>
                            <input type="email" name="correo" id="correo" value="{{ old('correo') }}" 
                                placeholder="ejemplo@empresa.com" 
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900/60 border border-slate-700 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition" 
                                required autofocus>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                Contraseña
                            </label>
                            <a href="#" class="text-xs font-medium text-cyan-400 hover:text-cyan-300 hover:underline transition">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                                <i class="bi bi-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" id="password" 
                                placeholder="••••••••" 
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900/60 border border-slate-700 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition" 
                                required>
                        </div>
                    </div>

                    <!-- Botón de Ingreso -->
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 active:bg-cyan-700 transition shadow-lg shadow-cyan-600/20 pt-3">
                        <i class="bi bi-box-arrow-in-right text-base"></i>
                        Ingresar al Sistema
                    </button>
                </form>

            </div>

            <!-- Footer de la Tarjeta -->
            <div class="px-8 py-4 bg-slate-900/40 border-t border-slate-700/50 text-center text-xs text-slate-500">
                © {{ date('Y') }} EconomyManager. Todos los derechos reservados.
            </div>

        </div>
    </div>

</body>
</html>