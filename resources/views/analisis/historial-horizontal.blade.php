
@extends('layouts.app')

@section('title', 'Historial de Análisis Horizontal')

@section('content')
<div class="space-y-6">

    <!-- ENCABEZADO -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">

        <div class="space-y-1">

            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}"
                   class="hover:text-cyan-600 transition">
                    Inicio
                </a>

                <i class="bi bi-chevron-right text-[10px]"></i>

                <a href="{{ route('analisis.index') }}"
                   class="hover:text-cyan-600 transition">
                    Análisis Financiero
                </a>

                <i class="bi bi-chevron-right text-[10px]"></i>

                <span class="text-slate-600 font-semibold">
                    Historial Horizontal
                </span>
            </nav>

            <div class="flex items-center gap-3">

                <div class="h-10 w-10 rounded-xl bg-slate-900 text-cyan-400 flex items-center justify-center">
                    <i class="bi bi-clock-history text-xl"></i>
                </div>

                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Historial de Análisis Horizontal
                    </h1>

                    <p class="text-xs text-slate-500">
                        Comparaciones disponibles según los estados financieros registrados
                    </p>
                </div>

            </div>
        </div>

        <a href="{{ route('analisis.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">

            <i class="bi bi-arrow-left"></i>
            Volver al panel

        </a>
    </div>

    <!-- RESUMEN -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Comparaciones Disponibles
            </p>

            <p class="text-2xl font-bold text-slate-900 mt-2">
                {{ $historicos->count() }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Balances Generales
            </p>

            <p class="text-2xl font-bold text-cyan-600 mt-2">
                {{ $historicos->where('tipo_reporte', 'balance')->count() }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Estados de Resultados
            </p>

            <p class="text-2xl font-bold text-emerald-600 mt-2">
                {{ $historicos->where('tipo_reporte', 'estado_resultados')->count() }}
            </p>
        </div>

    </div>

    <!-- TABLA DE HISTÓRICOS -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div>
                <h2 class="text-sm font-bold text-slate-900">
                    Análisis Horizontales Disponibles
                </h2>

                <p class="text-xs text-slate-500 mt-1">
                    Selecciona una comparación para consultar sus resultados
                </p>
            </div>

            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-cyan-50 text-cyan-700 text-xs font-semibold border border-cyan-100">
                <i class="bi bi-database"></i>
                Datos de MySQL
            </span>

        </div>

        <div class="w-full overflow-x-auto">

            <table class="w-full text-left border-collapse min-w-[750px]">

                <thead>
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider">

                        <th class="py-4 px-5 font-semibold">
                            Estado Financiero
                        </th>

                        <th class="py-4 px-5 font-semibold text-center">
                            Año Base
                        </th>

                        <th class="py-4 px-5 font-semibold text-center">
                            Año Actual
                        </th>

                        <th class="py-4 px-5 font-semibold text-center">
                            Período Comparado
                        </th>

                        <th class="py-4 px-5 font-semibold text-right">
                            Acción
                        </th>

                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-sm">

                    @forelse($historicos as $historico)

                        <tr class="hover:bg-slate-50 transition">

                            <!-- TIPO DE REPORTE -->
                            <td class="py-4 px-5">

                                <div class="flex items-center gap-3">

                                    <div class="h-9 w-9 rounded-lg flex items-center justify-center
                                        {{ $historico->tipo_reporte === 'balance'
                                            ? 'bg-cyan-50 text-cyan-700'
                                            : 'bg-emerald-50 text-emerald-700' }}">

                                        <i class="bi {{ $historico->tipo_reporte === 'balance'
                                            ? 'bi-journal-text'
                                            : 'bi-graph-up-arrow' }} text-lg"></i>

                                    </div>

                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            {{ $historico->nombre_reporte }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            Análisis Horizontal
                                        </p>
                                    </div>

                                </div>

                            </td>

                            <!-- AÑO BASE -->
                            <td class="py-4 px-5 text-center">

                                <span class="inline-flex px-3 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs font-bold">
                                    {{ $historico->anio_base }}
                                </span>

                            </td>

                            <!-- AÑO ACTUAL -->
                            <td class="py-4 px-5 text-center">

                                <span class="inline-flex px-3 py-1 rounded-lg bg-cyan-50 text-cyan-700 font-mono text-xs font-bold border border-cyan-100">
                                    {{ $historico->anio_actual }}
                                </span>

                            </td>

                            <!-- PERÍODO -->
                            <td class="py-4 px-5 text-center font-medium text-slate-600">

                                {{ $historico->anio_base }}
                                <i class="bi bi-arrow-right mx-1 text-slate-400"></i>
                                {{ $historico->anio_actual }}

                            </td>

                            <!-- ACCIÓN -->
                            <td class="py-4 px-5 text-right">

                                <a href="{{ route('analisis.horizontal', [
                                    'empresa_id' => $historico->empresa_id,
                                    'tipo_reporte' => $historico->tipo_reporte,
                                    'anio_base' => $historico->anio_base,
                                    'anio_actual' => $historico->anio_actual
                                ]) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 transition">

                                    <i class="bi bi-eye"></i>
                                    Ver análisis

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="py-16 text-center">

                                <div class="flex flex-col items-center justify-center gap-3 text-slate-400">

                                    <i class="bi bi-folder2-open text-4xl"></i>

                                    <p class="text-sm font-semibold text-slate-600">
                                        No hay comparaciones disponibles
                                    </p>

                                    <p class="text-xs max-w-sm">
                                        Se necesitan estados financieros de al menos dos años distintos del mismo tipo para realizar un análisis horizontal.
                                    </p>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
@endsection
