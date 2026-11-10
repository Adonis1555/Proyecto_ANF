@extends('layouts.app')

@section('title', 'Comparación por Sector')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <a href="{{ route('ratios.index') }}" class="hover:text-cyan-600 transition">Ratios Financieros</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Comparación por Sector</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-building text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Comparación de Empresas por Sector</h1>
                    <p class="text-xs text-slate-500">Evaluación de ratios contra los parámetros aceptables de cada rubro</p>
                </div>
            </div>
        </div>

        <a href="{{ route('ratios.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition self-start sm:self-auto">
            <i class="bi bi-arrow-left text-sm"></i>
            Volver a Ratios
        </a>
    </div>

    <!-- Tarjeta del Formulario de Búsqueda -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 max-w-3xl mx-auto space-y-5">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="h-9 w-9 rounded-lg bg-slate-900 text-cyan-400 flex items-center justify-center">
                <i class="bi bi-funnel text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-900">Parámetros de Filtrado</h3>
                <p class="text-xs text-slate-400">Selecciona el sector económico y el ejercicio fiscal a comparar</p>
            </div>
        </div>

        <form action="{{ route('comparacion.sector') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Selector Sector -->
                <div class="md:col-span-5 space-y-1.5">
                    <label for="sector_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Sector Económico:
                    </label>
                    <select name="sector_id" id="sector_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                        <option value="" selected disabled>-- Seleccione --</option>
                        @foreach ($sectores as $s)
                            <option value="{{ $s->id_sector }}">{{ $s->nombre_sector }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Selector Año -->
                <div class="md:col-span-4 space-y-1.5">
                    <label for="anio" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Año Fiscal:
                    </label>
                    <select name="anio" id="anio" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                        <option value="" selected disabled>-- Seleccione --</option>
                        @foreach ($anios as $anio)
                            <option value="{{ $anio }}">{{ $anio }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Botón de Envío -->
                <div class="md:col-span-3">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 active:bg-cyan-800 transition shadow-sm">
                        <i class="bi bi-bar-chart-line text-sm"></i>
                        Comparar
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection