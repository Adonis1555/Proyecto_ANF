@extends('layouts.app')

@section('title', $titulo ?? 'Ratios Financieros')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <a href="{{ route('ratios.index') }}" class="hover:text-cyan-600 transition">Ratios Financieros</a>
                @if(isset($anioSeleccionado))
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span class="text-slate-600 font-semibold">Año {{ $anioSeleccionado }}</span>
                @endif
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-calculator-fill text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        {{ $titulo }} @if(isset($anioSeleccionado)) — Año {{ $anioSeleccionado }} @endif
                    </h1>
                    <p class="text-xs text-slate-500">Indicadores clave de liquidez, solvencia, rentabilidad y actividad</p>
                </div>
            </div>
        </div>

        @if(isset($ratiosCalculados))
            <a href="{{ route('ratios.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition self-start sm:self-auto print:hidden">
                <i class="bi bi-arrow-left text-sm"></i>
                Consultar otro año
            </a>
        @endif
    </div>

    <!-- CASO 1: Sin años disponibles para calcular -->
    @if(empty($aniosDisponibles) && !isset($ratiosCalculados))
        <div class="flex items-center gap-3 p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm shadow-sm">
            <i class="bi bi-exclamation-triangle-fill text-amber-600 text-xl shrink-0"></i>
            <div>
                <strong class="font-bold block text-amber-900">Atención requerida</strong>
                <span>No hay años disponibles con Estado de Resultados y Balance General registrados para calcular ratios financieros.</span>
            </div>
        </div>

    <!-- CASO 2: Selección de Año -->
    @elseif(!isset($ratiosCalculados))
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5 max-w-xl mx-auto">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <div class="h-9 w-9 rounded-lg bg-slate-900 text-cyan-400 flex items-center justify-center">
                    <i class="bi bi-calendar-event text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Seleccionar Año para Análisis</h3>
                    <p class="text-xs text-slate-400">Elige un periodo contable para generar los indicadores</p>
                </div>
            </div>

            <form action="{{ route('ratios.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Años Disponibles:
                    </label>
                    <select name="anio" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                        <option value="" selected disabled>Seleccione un año</option>
                        @foreach($aniosDisponibles as $anio)
                            <option value="{{ $anio }}">{{ $anio }}</option>
                        @endforeach
                    </select>
                </div>

                @error('anio')
                    <p class="text-xs text-rose-600 font-medium flex items-center gap-1">
                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror

                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                        <i class="bi bi-calculator text-sm"></i>
                        Consultar Ratios Financieros
                    </button>
                </div>
            </form>
        </div>

    <!-- CASO 3: Visualización de Resultados Calculados -->
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-full">
                    <thead>
                        <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                            <th class="py-3.5 px-6 font-semibold w-7/12">Ratio Financiero</th>
                            <th class="py-3.5 px-6 font-semibold w-5/12 text-center">Valor Calculado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @if(count($ratiosCalculados) > 0)
                            
                            {{-- Sección: Ratios de Liquidez --}}
                            @php
                                $ratiosLiquidez = ['Liquidez Corriente', 'Prueba Ácida', 'Capital de Trabajo'];
                                $tieneRatiosLiquidez = array_intersect(array_keys($ratiosCalculados), $ratiosLiquidez);
                            @endphp
                            @if(count($tieneRatiosLiquidez) > 0)
                                <tr class="bg-slate-50 border-y border-slate-200/80">
                                    <td colspan="2" class="py-2.5 px-6 text-xs font-bold uppercase tracking-wider text-slate-700 bg-slate-100/60">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-droplet-fill text-cyan-600"></i>
                                            <span>Ratios de Liquidez</span>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($ratiosLiquidez as $nombreRatio)
                                    @if(isset($ratiosCalculados[$nombreRatio]))
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3.5 px-6 pl-10 text-slate-800 font-medium">
                                                {{ $nombreRatio }}
                                            </td>
                                            <td class="py-3.5 px-6 text-center">
                                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg bg-amber-50 border border-amber-200/80 text-amber-900 font-mono font-bold text-xs shadow-2xs">
                                                    {{ $ratiosCalculados[$nombreRatio] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif

                            {{-- Sección: Ratios de Endeudamiento --}}
                            @php
                                $ratiosEndeudamiento = ['Grado de Endeudamiento', 'Grado de Propiedad', 'Endeudamiento Patrimonial'];
                                $tieneRatiosEndeudamiento = array_intersect(array_keys($ratiosCalculados), $ratiosEndeudamiento);
                            @endphp
                            @if(count($tieneRatiosEndeudamiento) > 0)
                                <tr class="bg-slate-50 border-y border-slate-200/80">
                                    <td colspan="2" class="py-2.5 px-6 text-xs font-bold uppercase tracking-wider text-slate-700 bg-slate-100/60">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-bank2 text-cyan-600"></i>
                                            <span>Ratios de Endeudamiento</span>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($ratiosEndeudamiento as $nombreRatio)
                                    @if(isset($ratiosCalculados[$nombreRatio]))
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3.5 px-6 pl-10 text-slate-800 font-medium">
                                                {{ $nombreRatio }}
                                            </td>
                                            <td class="py-3.5 px-6 text-center">
                                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg bg-amber-50 border border-amber-200/80 text-amber-900 font-mono font-bold text-xs shadow-2xs">
                                                    {{ $ratiosCalculados[$nombreRatio] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif

                            {{-- Sección: Ratios de Rentabilidad --}}
                            @php
                                $ratiosRentabilidad = ['Rentabilidad sobre Ventas'];
                                $tieneRatiosRentabilidad = array_intersect(array_keys($ratiosCalculados), $ratiosRentabilidad);
                            @endphp
                            @if(count($tieneRatiosRentabilidad) > 0)
                                <tr class="bg-slate-50 border-y border-slate-200/80">
                                    <td colspan="2" class="py-2.5 px-6 text-xs font-bold uppercase tracking-wider text-slate-700 bg-slate-100/60">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-cash-stack text-cyan-600"></i>
                                            <span>Ratios de Rentabilidad</span>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($ratiosRentabilidad as $nombreRatio)
                                    @if(isset($ratiosCalculados[$nombreRatio]))
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3.5 px-6 pl-10 text-slate-800 font-medium">
                                                {{ $nombreRatio }}
                                            </td>
                                            <td class="py-3.5 px-6 text-center">
                                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg bg-amber-50 border border-amber-200/80 text-amber-900 font-mono font-bold text-xs shadow-2xs">
                                                    {{ $ratiosCalculados[$nombreRatio] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif

                            {{-- Sección: Ratios de Actividad --}}
                            @php
                                $ratiosActividad = ['Rotación de Inventario', 'Días de Inventario', 'Rotación de Cuentas por Cobrar', 'Periodo Medio de Cobranza', 'Rotación de Activos Totales'];
                                $tieneRatiosActividad = array_intersect(array_keys($ratiosCalculados), $ratiosActividad);
                            @endphp
                            @if(count($tieneRatiosActividad) > 0)
                                <tr class="bg-slate-50 border-y border-slate-200/80">
                                    <td colspan="2" class="py-2.5 px-6 text-xs font-bold uppercase tracking-wider text-slate-700 bg-slate-100/60">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-arrow-repeat text-cyan-600"></i>
                                            <span>Ratios de Actividad</span>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($ratiosActividad as $nombreRatio)
                                    @if(isset($ratiosCalculados[$nombreRatio]))
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3.5 px-6 pl-10 text-slate-800 font-medium">
                                                {{ $nombreRatio }}
                                            </td>
                                            <td class="py-3.5 px-6 text-center">
                                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg bg-amber-50 border border-amber-200/80 text-amber-900 font-mono font-bold text-xs shadow-2xs">
                                                    {{ $ratiosCalculados[$nombreRatio] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif

                        @else
                            <tr>
                                <td colspan="2" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <i class="bi bi-calculator-fill text-4xl text-slate-300"></i>
                                        <p class="text-sm font-medium text-slate-500">No se pudieron calcular ratios para el año seleccionado.</p>
                                        <p class="text-xs text-slate-400">Asegúrate de que los saldos necesarios estén registrados en el sistema.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
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