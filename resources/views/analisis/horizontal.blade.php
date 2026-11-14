
@extends('layouts.app')

@section('title', 'Análisis Horizontal')

@section('content')
<div class="space-y-6">

    <!-- ENCABEZADO -->
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
                <div class="h-10 w-10 rounded-xl bg-slate-900 text-cyan-400 flex items-center justify-center">
                    <i class="bi bi-arrow-left-right text-xl"></i>
                </div>

                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Análisis Horizontal
                    </h1>

                    <p class="text-xs text-slate-500">
                        {{ $tipoReporte === 'balance' ? 'Balance General' : 'Estado de Resultados' }}
                        · Comparativo {{ $anioBase }} vs {{ $anioActual }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 print:hidden">
            <a href="{{ route('analisis.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>

            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 transition">
                <i class="bi bi-printer"></i>
                Imprimir
            </button>
        </div>
    </div>

    <!-- RESUMEN -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Cuentas Comparadas
            </p>

            <p class="text-2xl font-bold text-slate-900 mt-2">
                {{ $comparativa->count() }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Variaciones Positivas
            </p>

            <p class="text-2xl font-bold text-emerald-600 mt-2">
                {{ $comparativa->filter(fn($fila) => $fila->variacion_absoluta > 0)->count() }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Variaciones Negativas
            </p>

            <p class="text-2xl font-bold text-red-600 mt-2">
                {{ $comparativa->filter(fn($fila) => $fila->variacion_absoluta < 0)->count() }}
            </p>
        </div>

    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">
                    Comparación de Estados Financieros
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Variación monetaria y porcentual por cuenta contable
                </p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[950px]">

                <thead>
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider">
                        <th class="py-4 px-5 font-semibold">Código</th>
                        <th class="py-4 px-5 font-semibold">Cuenta Contable</th>
                        <th class="py-4 px-5 font-semibold text-right">
                            {{ $anioBase }}
                        </th>
                        <th class="py-4 px-5 font-semibold text-right">
                            {{ $anioActual }}
                        </th>
                        <th class="py-4 px-5 font-semibold text-right">
                            Variación Absoluta ($)
                        </th>
                        <th class="py-4 px-5 font-semibold text-right bg-cyan-950/40 text-cyan-300">
                            Variación Relativa (%)
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-sm">

                    @forelse($comparativa as $fila)
                        <tr class="hover:bg-slate-50 transition">

                            <td class="py-3.5 px-5">
                                <span class="font-mono text-xs font-bold text-cyan-700 bg-cyan-50 border border-cyan-100 px-2 py-1 rounded-md">
                                    {{ $fila->codigo_cuenta }}
                                </span>
                            </td>

                            <td class="py-3.5 px-5 font-medium text-slate-800">
                                {{ $fila->nombre_cuenta }}
                            </td>

                            <td class="py-3.5 px-5 text-right font-mono text-slate-700">
                                $ {{ number_format($fila->saldo_base, 2, '.', ',') }}
                            </td>

                            <td class="py-3.5 px-5 text-right font-mono font-semibold text-slate-900">
                                $ {{ number_format($fila->saldo_actual, 2, '.', ',') }}
                            </td>

                            <td class="py-3.5 px-5 text-right font-mono font-semibold
                                {{ $fila->variacion_absoluta > 0 ? 'text-emerald-600' : ($fila->variacion_absoluta < 0 ? 'text-red-600' : 'text-slate-500') }}">
                                {{ $fila->variacion_absoluta > 0 ? '+' : '' }}
                                $ {{ number_format($fila->variacion_absoluta, 2, '.', ',') }}
                            </td>

                            <td class="py-3.5 px-5 text-right font-mono font-bold bg-cyan-50/30
                                {{ $fila->variacion_relativa > 0 ? 'text-emerald-700' : ($fila->variacion_relativa < 0 ? 'text-red-700' : 'text-slate-600') }}">

                                @if($fila->variacion_relativa === null)
                                    <span class="text-slate-400" title="El saldo del año base es cero">
                                        N/A
                                    </span>
                                @else
                                    {{ $fila->variacion_relativa > 0 ? '+' : '' }}
                                    {{ number_format($fila->variacion_relativa, 2, '.', ',') }} %
                                @endif

                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="py-14 text-center">
                                <div class="flex flex-col items-center justify-center gap-2 text-slate-400">
                                    <i class="bi bi-file-earmark-bar-graph text-4xl"></i>
                                    <p class="text-sm font-semibold text-slate-600">
                                        No hay información para comparar
                                    </p>
                                    <p class="text-xs">
                                        Verifique que existan reportes financieros de ambos años.
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
