@extends('layouts.app')

@section('title', 'Estado de Resultados - ' . $anio)

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
                <span class="text-slate-600 font-semibold">Estado de Resultados {{ $anio }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-file-earmark-bar-graph-fill text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Estado de Resultados — Año {{ $anio }}</h1>
                    <p class="text-xs text-slate-500">Desglose oficial de ingresos, costos y gastos del ejercicio fiscal</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            @if ($estadoResultados->isEmpty())
                <a href="{{ route('balance.importarVista', ['anio' => $anio]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                    <i class="bi bi-file-earmark-excel text-sm"></i>
                    Importar Excel
                </a>
            @endif
            <a href="{{ route('estado-resultados.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                <i class="bi bi-arrow-left text-sm"></i>
                Volver a Lista
            </a>
        </div>
    </div>

    <!-- Tabla de Cuentas y Saldos -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table id="reporte" class="w-full text-left border-collapse min-w-full">
                <thead>
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                        <th scope="col" class="py-3.5 px-6 font-semibold w-2/12">Código</th>
                        <th scope="col" class="py-3.5 px-6 font-semibold w-7/12">Cuenta Contable</th>
                        <th scope="col" class="py-3.5 px-6 font-semibold w-3/12 text-right">Saldo ($)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($estadoResultados as $index => $reporteFinanciero)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Código de Cuenta -->
                            <td class="py-3.5 px-6 font-mono font-bold text-cyan-600 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md bg-cyan-50 border border-cyan-200/60">
                                    {{ $reporteFinanciero->catalogo->codigo_cuenta }}
                                </span>
                            </td>

                            <!-- Nombre de Cuenta -->
                            <td class="py-3.5 px-6 font-medium text-slate-800">
                                {{ $reporteFinanciero->catalogo->nombre_cuenta }}
                            </td>

                            <!-- Saldo -->
                            <td class="py-3.5 px-6 text-right font-mono font-semibold text-slate-900 whitespace-nowrap">
                                {{ $reporteFinanciero->saldo == 0.123456789 ? '-' : '$ ' . number_format($reporteFinanciero->saldo, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="bi bi-file-earmark-x text-4xl text-slate-300"></i>
                                    <p class="text-sm font-medium text-slate-500">¡No existe Estado de Resultados registrado para este año!</p>
                                    <p class="text-xs text-slate-400">Comienza importando la plantilla Excel con el ejercicio contable.</p>
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