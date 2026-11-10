@extends('layouts.app')

@section('title', 'Gestión de Sectores')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Sectores Económicos</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-diagram-3 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Gestión de Sectores Económicos</h1>
                    <p class="text-xs text-slate-500">Mantenimiento de rubros comerciales y configuración de parámetros financieros de referencia</p>
                </div>
            </div>
        </div>

        <button type="button" onclick="document.getElementById('modalAgregar').showModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm self-start sm:self-auto">
            <i class="bi bi-plus-lg text-sm"></i>
            Agregar Sector
        </button>
    </div>

    <!-- Alertas de Sesión -->
    @if(session('success'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-check-circle-fill text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="text-emerald-600 hover:text-emerald-900 transition" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <!-- Tabla de Sectores -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-full">
                <thead>
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3.5 px-6 font-semibold w-2/12 text-center">ID</th>
                        <th class="py-3.5 px-6 font-semibold w-6/12">Nombre del Sector Económico</th>
                        <th class="py-3.5 px-6 font-semibold w-4/12 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($sectores as $sector)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-6 text-center font-mono text-slate-500 font-medium">
                                {{ $sector->id_sector }}
                            </td>
                            <td class="py-3.5 px-6 font-bold text-slate-900">
                                {{ $sector->nombre_sector }}
                            </td>
                            <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('sector.parametros', $sector->id_sector) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200/60 transition" title="Configurar parámetros del sector">
                                        <i class="bi bi-sliders"></i> Parámetros
                                    </a>

                                    <button type="button" onclick="document.getElementById('modalEditar{{ $sector->id_sector }}').showModal()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/60 transition">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </button>

                                    <form action="{{ route('sector.destroy', $sector->id_sector) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar el sector {{ $sector->nombre_sector }}?\nEsta acción afectará a las empresas asociadas.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Editar Sector (<dialog> Nativo Centrado) -->
                        <dialog id="modalEditar{{ $sector->id_sector }}" class="fixed inset-0 m-auto rounded-2xl p-0 backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm max-w-md w-full border-0 shadow-2xl overflow-hidden text-left">
                            <form method="POST" action="{{ route('sector.update', $sector->id_sector) }}" class="w-full space-y-0">
                                @csrf
                                @method('PUT')

                                <div class="bg-slate-900 px-6 py-4 border-b border-slate-800 flex items-center justify-between text-white">
                                    <h5 class="font-bold text-base flex items-center gap-2">
                                        <i class="bi bi-pencil-square text-cyan-400"></i>
                                        Editar Sector
                                    </h5>
                                    <button type="button" onclick="document.getElementById('modalEditar{{ $sector->id_sector }}').close()" class="text-slate-400 hover:text-white transition">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>

                                <div class="p-6 space-y-4 bg-white">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre del Sector</label>
                                        <input type="text" name="nombre_sector" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" value="{{ $sector->nombre_sector }}" required>
                                    </div>
                                </div>

                                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                                    <button type="button" onclick="document.getElementById('modalEditar{{ $sector->id_sector }}').close()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm">
                                        <i class="bi bi-x-circle text-sm"></i> Cancelar
                                    </button>
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                                        <i class="bi bi-check-circle text-sm"></i> Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </dialog>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="bi bi-diagram-3 text-4xl text-slate-300"></i>
                                    <p class="text-sm font-medium text-slate-500">No hay sectores económicos registrados.</p>
                                    <p class="text-xs text-slate-400">Presiona el botón "Agregar Sector" para definir un nuevo rubro.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Agregar Sector (<dialog> Nativo Centrado) -->
    <dialog id="modalAgregar" class="fixed inset-0 m-auto rounded-2xl p-0 backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm max-w-md w-full border-0 shadow-2xl overflow-hidden text-left">
        <form method="POST" action="{{ route('sector.store') }}" class="w-full space-y-0">
            @csrf

            <div class="bg-slate-900 px-6 py-4 border-b border-slate-800 flex items-center justify-between text-white">
                <h5 class="font-bold text-base flex items-center gap-2">
                    <i class="bi bi-plus-circle text-cyan-400"></i>
                    Agregar Sector Económico
                </h5>
                <button type="button" onclick="document.getElementById('modalAgregar').close()" class="text-slate-400 hover:text-white transition">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-4 bg-white">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre del Sector</label>
                    <input type="text" name="nombre_sector" placeholder="Ej. Comercio Minorista, Agroindustria, Servicios" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                </div>
            </div>

            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalAgregar').close()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm">
                    <i class="bi bi-x-circle text-sm"></i> Cancelar
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                    <i class="bi bi-plus-lg text-sm"></i> Registrar Sector
                </button>
            </div>
        </form>
    </dialog>

</div>
@endsection