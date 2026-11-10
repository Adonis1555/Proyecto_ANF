@extends('layouts.app')

@section('title', 'Análisis Vertical')

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
                <span class="text-slate-600 font-semibold">Análisis Vertical</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-border-width text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Análisis Vertical — {{ ucfirst(str_replace('_', ' ', $tipoReporte)) }} {{ $anio }}
                    </h1>
                    <p class="text-xs text-slate-500">Composición porcentual de las cuentas con respecto a la cuenta base del periodo</p>
                </div>
            </div>
        </div>

        <a href="{{ route('analisis.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition self-start sm:self-auto print:hidden">
            <i class="bi bi-arrow-left text-sm"></i>
            Volver
        </a>
    </div>

    <!-- Tabla de Análisis Vertical -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-full">
                <thead>
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3.5 px-6 font-semibold w-2/12">Código</th>
                        <th class="py-3.5 px-6 font-semibold w-6/12">Cuenta Contable</th>
                        <th class="py-3.5 px-6 font-semibold w-2/12 text-right">Saldo ($)</th>
                        <th class="py-3.5 px-6 font-semibold w-2/12 text-center bg-cyan-950/40 text-cyan-300">% Participación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    {{-- Reemplazar este bloque iterador con la variable enviada por tu controlador --}}
                    @forelse ($cuentas ?? [] as $cuenta)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-6 font-mono font-bold text-cyan-600">
                                <span class="px-2 py-0.5 rounded bg-cyan-50 border border-cyan-200/60 text-xs">
                                    {{ $cuenta->codigo }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 font-medium text-slate-800">
                                {{ $cuenta->nombre }}
                            </td>
                            <td class="py-3.5 px-6 text-right font-mono font-semibold text-slate-900">
                                $ {{ number_format($cuenta->saldo, 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-6 text-center font-mono font-bold text-cyan-700 bg-cyan-50/30">
                                {{ number_format($cuenta->porcentaje, 2) }} %
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="bi bi-file-earmark-bar-graph text-4xl text-slate-300"></i>
                                    <p class="text-sm font-medium text-slate-500">Vista previa del reporte de Análisis Vertical</p>
                                    <p class="text-xs text-slate-400">Conecta la variable $cuentas desde tu controlador para renderizar la matriz de saldos.</p>
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