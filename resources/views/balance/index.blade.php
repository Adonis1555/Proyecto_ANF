@extends('layouts.app')

@section('title', 'Listado de Balances General')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Listado de Balance General</span>
            </nav>
            <div class="flex items-center gap-2.5">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-calculator-fill text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Balances Generales</h1>
                    <p class="text-xs text-slate-500">Gestión de estados de situación financiera registrados</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button type="button" onclick="document.getElementById('analisisModal').showModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition shadow-sm">
                <i class="bi bi-bar-chart-line text-sm"></i>
                Ver análisis
            </button>
            <a href="{{ route('balance.importarVista') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                <i class="bi bi-file-earmark-excel text-sm"></i>
                Importar Excel
            </a>
        </div>
    </div>

    <!-- Alertas del Sistema -->
    @if(session('success'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-check-circle-fill text-emerald-600 text-base"></i>
                <div>
                    <strong class="font-bold">¡Éxito!</strong>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            <button type="button" class="text-emerald-600 hover:text-emerald-900 transition" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-x-circle-fill text-rose-600 text-base"></i>
                <div>
                    <strong class="font-bold">¡Error!</strong>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            <button type="button" class="text-rose-600 hover:text-rose-900 transition" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-exclamation-triangle-fill text-amber-600 text-base"></i>
                <div>
                    <strong class="font-bold">¡Advertencia!</strong>
                    <span>{{ session('warning') }}</span>
                </div>
            </div>
            <button type="button" class="text-amber-600 hover:text-amber-900 transition" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
            <div class="flex items-center gap-2 mb-2 font-bold text-rose-900">
                <i class="bi bi-exclamation-octagon-fill text-rose-600"></i>
                <span>Por favor corrige los siguientes errores:</span>
            </div>
            <ul class="list-disc pl-5 space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabla de Balances Registrados -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-full">
                <thead>
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3.5 px-6 font-semibold w-1/12 text-center">N°</th>
                        <th class="py-3.5 px-6 font-semibold w-6/12">Nombre del Reporte</th>
                        <th class="py-3.5 px-6 font-semibold w-2/12 text-center">Año Contable</th>
                        <th class="py-3.5 px-6 font-semibold w-3/12 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($anios as $index => $anio)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-6 text-center font-mono text-slate-500 font-medium">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-slate-800 flex items-center gap-2">
                                <i class="bi bi-file-earmark-text text-cyan-600"></i>
                                Balance General
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <span class="px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 font-mono font-bold text-slate-700">
                                    {{ $anio }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('balance.ver', $anio) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/60 transition">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>

                                    <form action="{{ route('balance.eliminar', $anio) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar el Balance General del año {{ $anio }}?\nSe eliminarán todos los reportes financieros y ratios asociados.\nEsta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="bi bi-calculator text-4xl text-slate-300"></i>
                                    <p class="text-sm font-medium text-slate-500">No hay balances generales registrados.</p>
                                    <p class="text-xs text-slate-400">Importa una plantilla Excel para comenzar la evaluación de periodos.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Análisis Financiero (<dialog> Nativo Centrado) -->
    <dialog id="analisisModal" class="fixed inset-0 m-auto rounded-2xl p-0 backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm max-w-md w-full border-0 shadow-2xl overflow-hidden">
        <form method="GET" action="{{ route('balance.analisis') }}" class="w-full">
            <div class="bg-slate-900 px-6 py-4 border-b border-slate-800 flex items-center justify-between text-white">
                <h5 class="font-bold text-base flex items-center gap-2">
                    <i class="bi bi-bar-chart-line text-cyan-400"></i>
                    Análisis Comparativo de Balance
                </h5>
                <button type="button" onclick="document.getElementById('analisisModal').close()" class="text-slate-400 hover:text-white transition">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4 bg-white">
                <div>
                    <label for="pasado" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Año Inicial (Desde)</label>
                    <input type="number" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition" id="pasado" name="pasado" required min="2017" placeholder="Ej: 2021">
                </div>
                <div>
                    <label for="actual" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Año Final (Hasta)</label>
                    <input type="number" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition" id="actual" name="actual" required min="2017" placeholder="Ej: 2024">
                </div>
            </div>
            
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('analisisModal').close()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm">
                    <i class="bi bi-x-circle text-sm"></i> Cancelar
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                    <i class="bi bi-arrow-right-circle text-sm"></i> Ir al Análisis
                </button>
            </div>
        </form>
    </dialog>

</div>
@endsection