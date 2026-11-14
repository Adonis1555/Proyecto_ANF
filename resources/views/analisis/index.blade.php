
@extends('layouts.app')

@section('title', 'Análisis Financiero')

@section('content')
<div class="space-y-6">

    <!-- ENCABEZADO -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Análisis Financiero</span>
            </nav>

            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center">
                    <i class="bi bi-bar-chart-steps text-xl"></i>
                </div>

                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Análisis Financiero Estructural
                    </h1>
                    <p class="text-xs text-slate-500">
                        Evaluación vertical y horizontal de los estados financieros institucionales
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ERRORES DE VALIDACIÓN -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
            <div class="flex items-center gap-2 font-bold mb-2">
                <i class="bi bi-exclamation-triangle"></i>
                No se pudo generar el análisis
            </div>

            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORMULARIOS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- ANÁLISIS VERTICAL -->
        <div id="analisis-vertical" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col scroll-mt-24">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-5">
                <div class="h-10 w-10 rounded-lg bg-cyan-500/10 text-cyan-600 flex items-center justify-center">
                    <i class="bi bi-border-width text-lg"></i>
                </div>

                <div>
                    <h3 class="font-bold text-sm text-slate-900">Análisis Vertical</h3>
                    <p class="text-xs text-slate-400">
                        Participación porcentual de las cuentas en un ejercicio fiscal
                    </p>
                </div>
            </div>

            <form action="{{ route('analisis.vertical') }}" method="GET" class="space-y-4 flex-1 flex flex-col">

                <div>
                    <label for="empresa_id_v" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Empresa *
                    </label>

                    <select name="empresa_id" id="empresa_id_v"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-cyan-500 outline-none"
                            required>
                        <option value="" disabled selected>Seleccione una empresa</option>

                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id_empresa }}">
                                {{ $empresa->nombre_empresa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tipo_reporte_v" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Estado Financiero *
                    </label>

                    <select name="tipo_reporte" id="tipo_reporte_v"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-cyan-500 outline-none"
                            required>
                        <option value="balance">Balance General</option>
                        <option value="estado_resultados">Estado de Resultados</option>
                    </select>
                </div>

                <div>
                    <label for="anio_v" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Año Fiscal *
                    </label>

                    <select name="anio" id="anio_v"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-cyan-500 outline-none"
                            required>
                        <option value="">Seleccione un año</option>
                    </select>
                </div>

                <div class="pt-3 mt-auto">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                        <i class="bi bi-calculator text-sm"></i>
                        Generar Análisis Vertical
                    </button>
                </div>
            </form>
        </div>

        <!-- ANÁLISIS HORIZONTAL -->
        <div id="analisis-horizontal" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col scroll-mt-24">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-5">
                <div class="h-10 w-10 rounded-lg bg-slate-900 text-cyan-400 flex items-center justify-center">
                    <i class="bi bi-arrows-expand text-lg"></i>
                </div>

                <div>
                    <h3 class="font-bold text-sm text-slate-900">Análisis Horizontal</h3>
                    <p class="text-xs text-slate-400">
                        Comparación de saldos y variaciones entre dos periodos
                    </p>
                </div>
            </div>

            <form action="{{ route('analisis.horizontal') }}" method="GET" class="space-y-4 flex-1 flex flex-col">

                <div>
                    <label for="empresa_id_h" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Empresa *
                    </label>

                    <select name="empresa_id" id="empresa_id_h"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-cyan-500 outline-none"
                            required>
                        <option value="" disabled selected>Seleccione una empresa</option>

                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id_empresa }}">
                                {{ $empresa->nombre_empresa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tipo_reporte_h" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Estado Financiero *
                    </label>

                    <select name="tipo_reporte" id="tipo_reporte_h"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-cyan-500 outline-none"
                            required>
                        <option value="balance">Balance General</option>
                        <option value="estado_resultados">Estado de Resultados</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label for="anio_base" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Año Base *
                        </label>

                        <select name="anio_base" id="anio_base"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-cyan-500 outline-none"
                                required>
                            <option value="">Seleccione un año</option>
                        </select>
                    </div>

                    <div>
                        <label for="anio_actual" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Año a Comparar *
                        </label>

                        <select name="anio_actual" id="anio_actual"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-cyan-500 outline-none"
                                required>
                            <option value="">Seleccione un año</option>
                        </select>
                    </div>

                </div>

                <p id="mensaje_anios" class="text-xs text-slate-500">
                    Seleccione dos años diferentes para realizar la comparación.
                </p>

                <div class="pt-3 mt-auto">
                    <button type="submit" id="btn_horizontal"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition shadow-sm">
                        <i class="bi bi-arrow-left-right text-sm"></i>
                        Generar Análisis Horizontal
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- AÑOS DISPONIBLES DESDE MYSQL -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const aniosBalance = @json($aniosBalance ?? []);
    const aniosResultados = @json($aniosResultados ?? []);

    const tipoVertical = document.getElementById('tipo_reporte_v');
    const anioVertical = document.getElementById('anio_v');

    const tipoHorizontal = document.getElementById('tipo_reporte_h');
    const anioBase = document.getElementById('anio_base');
    const anioActual = document.getElementById('anio_actual');

    const mensaje = document.getElementById('mensaje_anios');
    const boton = document.getElementById('btn_horizontal');

    function obtenerAnios(tipo) {
        return (tipo === 'balance' ? aniosBalance : aniosResultados)
            .map(Number)
            .filter(Number.isFinite)
            .sort((a, b) => b - a);
    }

    function llenarSelect(select, anios, texto) {
        select.innerHTML = '';

        const inicial = document.createElement('option');
        inicial.value = '';
        inicial.textContent = texto;
        select.appendChild(inicial);

        anios.forEach(anio => {
            const opcion = document.createElement('option');
            opcion.value = anio;
            opcion.textContent = anio;
            select.appendChild(opcion);
        });
    }

    function actualizarVertical() {
        const anios = obtenerAnios(tipoVertical.value);

        llenarSelect(anioVertical, anios, 'Seleccione un año');
    }

    function actualizarHorizontal() {
        const anios = obtenerAnios(tipoHorizontal.value);

        llenarSelect(anioBase, anios, 'Año base');
        llenarSelect(anioActual, anios, 'Año actual');

        validarHorizontal();
    }

    function validarHorizontal() {
        const base = Number(anioBase.value);
        const actual = Number(anioActual.value);

        if (!anioBase.value || !anioActual.value) {
            mensaje.textContent = 'Seleccione dos años para realizar la comparación.';
            mensaje.className = 'text-xs text-slate-500';
            boton.disabled = true;
            boton.classList.add('opacity-50', 'cursor-not-allowed');
            return;
        }

        if (actual <= base) {
            mensaje.textContent = 'El año a comparar debe ser posterior al año base.';
            mensaje.className = 'text-xs text-red-600 font-semibold';
            boton.disabled = true;
            boton.classList.add('opacity-50', 'cursor-not-allowed');
            return;
        }

        mensaje.textContent = 'Periodos válidos para realizar el análisis.';
        mensaje.className = 'text-xs text-emerald-600 font-semibold';
        boton.disabled = false;
        boton.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    tipoVertical.addEventListener('change', actualizarVertical);
    tipoHorizontal.addEventListener('change', actualizarHorizontal);

    anioBase.addEventListener('change', validarHorizontal);
    anioActual.addEventListener('change', validarHorizontal);

    actualizarVertical();
    actualizarHorizontal();
});
</script>
@endsection
