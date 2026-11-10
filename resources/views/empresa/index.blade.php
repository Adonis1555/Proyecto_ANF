@extends('layouts.app')

@section('title', 'Gestión de Empresas')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Listado de Empresas</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-building-gear text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Gestión de Empresas</h1>
                    <p class="text-xs text-slate-500">Registro de entidades corporativas y asignación de sectores económicos</p>
                </div>
            </div>
        </div>

        <button type="button" onclick="document.getElementById('modalAgregar').showModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm self-start sm:self-auto">
            <i class="bi bi-plus-lg text-sm"></i>
            Agregar Empresa
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

    <!-- Tabla de Empresas -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-full">
                <thead>
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3.5 px-6 font-semibold w-1/12 text-center">ID</th>
                        <th class="py-3.5 px-6 font-semibold w-2/12">NIT</th>
                        <th class="py-3.5 px-6 font-semibold w-4/12">Nombre de la Empresa</th>
                        <th class="py-3.5 px-6 font-semibold w-3/12">Sector Económico</th>
                        <th class="py-3.5 px-6 font-semibold w-2/12 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($empresas as $empresa)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-6 text-center font-mono text-slate-500 font-medium">
                                {{ $empresa->id_empresa }}
                            </td>
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-700">
                                {{ $empresa->nit }}
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-slate-900">
                                {{ $empresa->nombre_empresa }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-600">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 text-xs font-medium text-slate-700">
                                    <i class="bi bi-diagram-3 text-cyan-600 text-[11px]"></i>
                                    {{ $empresa->sector->nombre_sector }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" onclick="document.getElementById('modalEditar{{ $empresa->id_empresa }}').showModal()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/60 transition">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </button>

                                    <form action="{{ route('empresa.destroy', $empresa->id_empresa) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar la empresa {{ $empresa->nombre_empresa }}?\nEsta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Editar Empresa (<dialog> Nativo Centrado) -->
                        <dialog id="modalEditar{{ $empresa->id_empresa }}" class="fixed inset-0 m-auto rounded-2xl p-0 backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm max-w-md w-full border-0 shadow-2xl overflow-hidden text-left">
                            <form method="POST" action="{{ route('empresa.update', $empresa->id_empresa) }}" class="w-full space-y-0">
                                @csrf
                                @method('PUT')

                                <div class="bg-slate-900 px-6 py-4 border-b border-slate-800 flex items-center justify-between text-white">
                                    <h5 class="font-bold text-base flex items-center gap-2">
                                        <i class="bi bi-pencil-square text-cyan-400"></i>
                                        Editar Empresa
                                    </h5>
                                    <button type="button" onclick="document.getElementById('modalEditar{{ $empresa->id_empresa }}').close()" class="text-slate-400 hover:text-white transition">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>

                                <div class="p-6 space-y-4 bg-white">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIT</label>
                                        <input type="text" name="nit" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" value="{{ $empresa->nit }}" required>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre de la Empresa</label>
                                        <input type="text" name="nombre_empresa" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" value="{{ $empresa->nombre_empresa }}" required>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Sector Económico</label>
                                        <select name="id_sector" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                                            @foreach ($sectores as $sector)
                                                <option value="{{ $sector->id_sector }}" {{ $empresa->id_sector == $sector->id_sector ? 'selected' : '' }}>
                                                    {{ $sector->nombre_sector }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                                    <button type="button" onclick="document.getElementById('modalEditar{{ $empresa->id_empresa }}').close()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm">
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
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="bi bi-building-x text-4xl text-slate-300"></i>
                                    <p class="text-sm font-medium text-slate-500">No hay empresas registradas.</p>
                                    <p class="text-xs text-slate-400">Presiona el botón "Agregar Empresa" para dar de alta una nueva entidad.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Agregar Empresa (<dialog> Nativo Centrado) -->
    <dialog id="modalAgregar" class="fixed inset-0 m-auto rounded-2xl p-0 backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm max-w-md w-full border-0 shadow-2xl overflow-hidden text-left">
        <form method="POST" action="{{ route('empresa.store') }}" class="w-full space-y-0">
            @csrf

            <div class="bg-slate-900 px-6 py-4 border-b border-slate-800 flex items-center justify-between text-white">
                <h5 class="font-bold text-base flex items-center gap-2">
                    <i class="bi bi-plus-circle text-cyan-400"></i>
                    Agregar Empresa
                </h5>
                <button type="button" onclick="document.getElementById('modalAgregar').close()" class="text-slate-400 hover:text-white transition">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-4 bg-white">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIT</label>
                    <input type="text" name="nit" placeholder="Ej. 0614-150220-101-0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre de la Empresa</label>
                    <input type="text" name="nombre_empresa" placeholder="Ej. Comercializadora S.A. de C.V." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Sector Económico</label>
                    <select name="id_sector" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white" required>
                        <option value="" disabled selected>Seleccione un sector</option>
                        @foreach ($sectores as $sector)
                            <option value="{{ $sector->id_sector }}">{{ $sector->nombre_sector }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalAgregar').close()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm">
                    <i class="bi bi-x-circle text-sm"></i> Cancelar
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                    <i class="bi bi-plus-lg text-sm"></i> Registrar Empresa
                </button>
            </div>
        </form>
    </dialog>

</div>
@endsection