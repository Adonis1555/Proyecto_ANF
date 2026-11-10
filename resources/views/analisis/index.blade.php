@extends('layouts.app')

@section('title', 'Análisis Financiero')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Análisis Financiero</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-bar-chart-steps text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Análisis Financiero Estructural</h1>
                    <p class="text-xs text-slate-500">Evaluación vertical y horizontal de los estados financieros institucionales</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Métodos de Análisis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Análisis Vertical -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div class="h-9 w-9 rounded-lg bg-cyan-500/10 text-cyan-600 flex items-center justify-center font-bold">
                        <i class="bi bi-border-width text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Análisis Vertical</h3>
                        <p class="text-xs text-slate-400">Estructura porcentual sobre un mismo ejercicio fiscal</p>
                    </div>
                </div>

                <form action="{{ route('analisis.vertical') }}" method="GET" class="space-y-4">
                    <div>
                        <label for="empresa_id_v" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Empresa *</label>
                        <select name="empresa_id" id="empresa_id_v" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                            <option value="" selected disabled>Seleccione una empresa</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id_empresa }}">{{ $empresa->nombre_empresa }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="anio_v" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Año Fiscal *</label>
                            <input type="number" name="anio" id="anio_v" placeholder="Ej. 2024" min="2000" max="2099" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                        </div>

                        <div>
                            <label for="tipo_reporte" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Reporte *</label>
                            <select name="tipo_reporte" id="tipo_reporte" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                                <option value="balance">Balance General</option>
                                <option value="estado_resultados">Estado de Resultados</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                            <i class="bi bi-calculator text-sm"></i> Generar Análisis Vertical
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Análisis Horizontal -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div class="h-9 w-9 rounded-lg bg-slate-900 text-cyan-400 flex items-center justify-center font-bold">
                        <i class="bi bi-arrows-expand text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Análisis Horizontal</h3>
                        <p class="text-xs text-slate-400">Variación absoluta y relativa entre dos periodos</p>
                    </div>
                </div>

                <form action="{{ route('analisis.horizontal') }}" method="GET" class="space-y-4">
                    <div>
                        <label for="empresa_id_h" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Empresa *</label>
                        <select name="empresa_id" id="empresa_id_h" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                            <option value="" selected disabled>Seleccione una empresa</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id_empresa }}">{{ $empresa->nombre_empresa }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="anio_base" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Año Base (Anterior) *</label>
                            <input type="number" name="anio_base" id="anio_base" placeholder="Ej. 2023" min="2000" max="2099" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                        </div>

                        <div>
                            <label for="anio_actual" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Año a Comparar *</label>
                            <input type="number" name="anio_actual" id="anio_actual" placeholder="Ej. 2024" min="2000" max="2099" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition shadow-sm">
                            <i class="bi bi-arrow-left-right text-sm"></i> Generar Análisis Horizontal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection