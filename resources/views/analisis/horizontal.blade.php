@extends('layouts.app')

@section('title', 'Análisis Horizontal')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <a href="{{ route('analisis.index') }}" class="hover:text-cyan-600 transition">Análisis Financiero</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Análisis Horizontal</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-slate-900 text-cyan-400 border border-slate-800 flex items-center justify-center font-bold">
                    <i class="bi bi-arrows-expand text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Análisis Horizontal — Comparativo {{ $anioBase }} vs {{ $anioActual }}
                    </h1>
                    <p class="text-xs text-slate-500">Evaluación de las variaciones absolutas ($) y relativas (%) entre dos periodos contables</p>
                </div>
            </div>
        </div>

        <a href="{{ route('analisis.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition self-start sm:self-auto print:hidden">
            <i class="bi bi-arrow-left text-sm"></i>
            Volver
        </a>
    </div>

    <!-- Tabla de Análisis Horizontal -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-full">
                <thead>
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3.5 px-6 font-semibold w-4/12">Cuenta Contable</th>
                        <th class="py-3.5 px-6 font-semibold text-center w-2/12 font-mono">Año {{ $anioBase }}</th>
                        <th class="py-3.5 px-6 font-semibold text-center w-2/12 font-mono">Año {{ $anioActual }}</th>
                        <th class="py-3.5 px-6 font-semibold text-center w-2/12">Var. Absoluta ($)</th>
                        <th class="py-3.5 px-6 font-semibold text-center w-2/12">Var. Relativa (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    {{-- Reemplazar este bloque iterador con la variable enviada por tu controlador --}}
                    @forelse ($comparativa ?? [] as $fila)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-6 font-semibold text-slate-800">
                                {{ $fila->nombre_cuenta }}
                            </td>
                            <td class="py-3.5 px-6 text-center font-mono text-slate-600">
                                $ {{ number_format($fila->saldo_base, 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-6 text-center font-mono text-slate-900 font-bold">
                                $ {{ number_format($fila->saldo_actual, 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-6 text-center font-mono font-bold whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md border text-xs {{ $fila->variacion_absoluta >= 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60' : 'bg-rose-50 text-rose-700 border-rose-200/60' }}">
                                    {{ $fila->variacion_absoluta >= 0 ? '+' : '' }}$ {{ number_format($fila->variacion_absoluta, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center font-mono font-bold whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md border text-xs {{ $fila->variacion_relativa >= 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60' : 'bg-rose-50 text-rose-700 border-rose-200/60' }}">
                                    {{ $fila->variacion_relativa >= 0 ? '+' : '' }}{{ number_format($fila->variacion_relativa, 2) }} %
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="bi bi-file-earmark-diff text-4xl text-slate-300"></i>
                                    <p class="text-sm font-medium text-slate-500">Vista previa del reporte de Análisis Horizontal</p>
                                    <p class="text-xs text-slate-400">Conecta la variable $comparativa desde tu controlador para desplegar las variaciones entre los dos periodos seleccionados.</p>
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