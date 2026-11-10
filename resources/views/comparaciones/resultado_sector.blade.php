@extends('layouts.app')

@section('title', 'Resultados por Sector')

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
                <a href="{{ url('/comparacion/sector') }}" class="hover:text-cyan-600 transition">Comparación por Sector</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Resultados {{ $nombre_sector }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-building-check text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Resultados por Sector — {{ $nombre_sector }}</h1>
                    <p class="text-xs text-slate-500">Evaluación de parámetros de referencia para el ejercicio fiscal {{ $anio }}</p>
                </div>
            </div>
        </div>

        <a href="{{ url('/comparacion/sector') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition self-start sm:self-auto print:hidden">
            <i class="bi bi-arrow-left text-sm"></i>
            Volver a Selección
        </a>
    </div>

    <!-- Tabla de Resultados o Estado Vacío -->
    @if (count($resultados) > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-full">
                    <thead>
                        <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                            <th scope="col" class="py-3.5 px-6 font-semibold min-w-[200px]">Ratio Financiero</th>
                            <th scope="col" class="py-3.5 px-6 font-semibold text-center bg-cyan-950/40 text-cyan-300 min-w-[140px]">
                                Valor Aceptable
                            </th>
                            @foreach ($empresas as $empresa)
                                <th scope="col" class="py-3.5 px-6 font-semibold text-center border-l border-slate-800/60 min-w-[150px]">
                                    {{ $empresa->nombre_empresa }}
                                </th>
                            @endforeach
                            <th scope="col" class="py-3.5 px-6 font-semibold text-center border-l border-slate-800/60 min-w-[180px]">
                                Empresas que Cumplen
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach ($resultados as $fila)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- Nombre del Ratio -->
                                <td class="py-3.5 px-6 font-semibold text-slate-800">
                                    {{ $fila['nombre_ratio'] }}
                                </td>

                                <!-- Valor Aceptable -->
                                <td class="py-3.5 px-6 text-center font-mono font-bold text-cyan-700 bg-cyan-50/30 whitespace-nowrap">
                                    {{ $fila['valor_aceptable'] ?? '-' }}
                                </td>

                                <!-- Valores por Empresa -->
                                @foreach ($empresas as $empresa)
                                    <td class="py-3.5 px-6 text-center font-mono text-slate-700 whitespace-nowrap border-l border-slate-100">
                                        @if(isset($fila['valores_empresas'][$empresa->nombre_empresa]))
                                            <span class="font-semibold text-slate-900">
                                                {{ $fila['valores_empresas'][$empresa->nombre_empresa] }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic text-xs">-</span>
                                        @endif
                                    </td>
                                @endforeach

                                <!-- Empresas que Cumplen -->
                                <td class="py-3.5 px-6 text-center border-l border-slate-100 whitespace-nowrap">
                                    @if (count($fila['cumplen']) > 0)
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 shadow-2xs">
                                            <i class="bi bi-check-circle-fill text-emerald-500 me-1.5 text-[11px]"></i>
                                            {{ implode(', ', $fila['cumplen']) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/60 shadow-2xs">
                                            <i class="bi bi-x-circle-fill text-rose-500 me-1.5 text-[11px]"></i>
                                            Ninguna
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center text-slate-400">
            <div class="flex flex-col items-center justify-center space-y-2">
                <i class="bi bi-exclamation-circle text-4xl text-amber-500"></i>
                <p class="text-sm font-medium text-slate-700">Sin datos registrados</p>
                <p class="text-xs text-slate-400">No se encontraron datos para el sector y año seleccionados.</p>
            </div>
        </div>
    @endif

</div>
@endsection

@push('styles')
<style>
    @media print {
        header, nav, .print\:hidden { display: none !important; }
        body { background-color: white !important; }
        .shadow-sm { box-shadow: none !important; }
        .border { border-color: #e2e8f0 !important; }
    }
</style>
@endpush