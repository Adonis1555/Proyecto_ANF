@extends('layouts.app')

@section('title', 'Análisis de Estado de Resultados')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <a href="{{ route('estado-resultados.index') }}" class="hover:text-cyan-600 transition">Lista de Estados de Resultados</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Análisis de ER</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-[#111827] bi-pie-chart-fill text-xl"></i>
                    <i class="bi bi-graph-up text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Análisis de Estado de Resultados</h1>
                    <p class="text-xs text-slate-500">Evaluación del periodo <span class="font-bold text-slate-700">{{ $anio_menor }}</span> al <span class="font-bold text-slate-700">{{ $anio_mayor }}</span></p>
                </div>
            </div>
        </div>

        <a href="{{ route('estado-resultados.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition self-start sm:self-auto print:hidden">
            <i class="bi bi-arrow-left text-sm"></i>
            Volver a Lista
        </a>
    </div>

    <!-- Tabla del Análisis Financiero -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table id="reporte" class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <!-- Fila 1: Grupos de Columnas -->
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                        <th rowspan="2" class="py-3.5 px-6 font-semibold align-middle w-3/12 border-r border-slate-800/60">Cuenta Contable</th>
                        <th colspan="2" class="py-2.5 px-4 font-semibold text-center border-r border-slate-800/60 bg-slate-800/40">Saldos Registrados</th>
                        <th colspan="2" class="py-2.5 px-4 font-semibold text-center border-r border-slate-800/60 bg-cyan-950/40 text-cyan-300">Análisis Horizontal</th>
                        <th colspan="2" class="py-2.5 px-4 font-semibold text-center bg-slate-800/40">Análisis Vertical</th>
                    </tr>
                    <!-- Fila 2: Subcolumnas por periodo y métrica -->
                    <tr class="bg-slate-900 text-slate-300 text-[11px] uppercase tracking-wider border-b border-slate-800">
                        <th class="py-2.5 px-4 font-semibold text-right border-r border-slate-800/60">{{ $anio_menor }}</th>
                        <th class="py-2.5 px-4 font-semibold text-right border-r border-slate-800/60">{{ $anio_mayor }}</th>
                        <th class="py-2.5 px-4 font-semibold text-right border-r border-slate-800/60 text-cyan-400">Var. Absoluta ($)</th>
                        <th class="py-2.5 px-4 font-semibold text-center border-r border-slate-800/60 text-cyan-400">Var. Relativa (%)</th>
                        <th class="py-2.5 px-4 font-semibold text-center border-r border-slate-800/60">{{ $anio_menor }} (%)</th>
                        <th class="py-2.5 px-4 font-semibold text-center">{{ $anio_mayor }} (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @if(isset($resultadosCompletos) && !empty($resultadosCompletos))
                        @foreach($resultadosCompletos as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- Cuenta -->
                                <td class="py-3.5 px-6 font-medium text-slate-800 border-r border-slate-100">
                                    {{ $item['cuenta'] }}
                                </td>
                                
                                <!-- Saldos -->
                                <td class="py-3.5 px-4 text-right font-mono text-slate-600 border-r border-slate-100">
                                    $ {{ number_format($item['saldo_menor'], 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-slate-900 border-r border-slate-100">
                                    $ {{ number_format($item['saldo_mayor'], 2) }}
                                </td>
                                
                                <!-- Análisis Horizontal -->
                                <td class="py-3.5 px-4 text-right font-mono font-semibold border-r border-slate-100 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 {{ $item['cambio'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        <i class="bi {{ $item['cambio'] >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }} text-xs"></i>
                                        $ {{ number_format($item['cambio'], 2) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center border-r border-slate-100 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-xs font-mono font-bold border {{ $item['porcentaje_cambio'] >= 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60' : 'bg-rose-50 text-rose-700 border-rose-200/60' }}">
                                        {{ number_format($item['porcentaje_cambio'], 2) }} %
                                    </span>
                                </td>
                                
                                <!-- Análisis Vertical -->
                                <td class="py-3.5 px-4 text-center font-mono text-slate-600 border-r border-slate-100">
                                    {{ number_format($item['vertical_menor'], 2) }} %
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-600">
                                    {{ number_format($item['vertical_mayor'], 2) }} %
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="bi bi-file-earmark-x text-4xl text-slate-300"></i>
                                    <p class="text-sm font-medium text-slate-500">¡No existe Estado de Resultados registrado para los años seleccionados!</p>
                                    <p class="text-xs text-slate-400">Verifica la selección de los periodos o importa la información correspondiente.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    @media print {
        header, nav, .print\:hidden { display: none !important; }
        body { background-color: white !important; }
        .shadow-sm, .shadow-2xl { box-shadow: none !important; }
        .border { border-color: #e2e8f0 !important; }
    }
</style>
@endpush