@extends('layouts.app')

@section('title', 'Parámetros del Sector')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <a href="{{ route('sector.index') }}" class="hover:text-cyan-600 transition">Sectores Económicos</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Parámetros</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-sliders text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Parámetros Aceptables por Sector</h1>
                    <p class="text-xs text-slate-500">Configuración de benchmarks financieros para: <span class="font-bold text-slate-800">{{ $sector->nombre_sector }}</span></p>
                </div>
            </div>
        </div>

        <a href="{{ route('sector.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition self-start sm:self-auto">
            <i class="bi bi-arrow-left text-sm"></i>
            Volver a Sectores
        </a>
    </div>

    <!-- Formulario de Configuración de Parámetros -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('sector.parametros.update', $sector->id_sector) }}" class="space-y-0">
            @csrf

            <!-- Tabla de Ratios y Valores -->
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-full">
                    <thead>
                        <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                            <th scope="col" class="py-3.5 px-6 font-semibold w-7/12">Ratio Financiero</th>
                            <th scope="col" class="py-3.5 px-6 font-semibold w-5/12 text-center">Valor Aceptable (Benchmark)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach ($ratios as $ratio)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- Nombre del Ratio -->
                                <td class="py-3.5 px-6 font-medium text-slate-800">
                                    {{ $ratio->nombre_ratio }}
                                </td>

                                <!-- Campo de entrada de valor -->
                                <td class="py-3 px-6 text-center">
                                    <div class="max-w-xs mx-auto">
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            name="ratios[{{ $ratio->id_ratio }}]" 
                                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-mono font-bold text-slate-900 text-center focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" 
                                            value="{{ $valoresExistentes[$ratio->id_ratio] ?? '' }}"
                                            placeholder="Ej. 1.50">
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Botones de Acción de Pie de Página -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('sector.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm">
                    <i class="bi bi-x-circle text-sm"></i>
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                    <i class="bi bi-check-circle text-sm"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

</div>
@endsection