@extends('layouts.app')

@section('title', 'Catálogo de Cuentas')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-journal-bookmark-fill text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Catálogo de Cuentas</h1>
                    <p class="text-xs text-slate-500">Gestión estructurada del plan contable institucional</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="text-xs font-medium text-slate-500">Empresa:</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                <i class="bi bi-building text-cyan-600"></i>
                {{ $empresaNombre }}
            </span>
        </div>
    </div>

    <!-- Alertas del Sistema -->
    @if (session('success'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-check-circle-fill text-emerald-600 text-base"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" class="text-emerald-600 hover:text-emerald-900 transition" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if (session('warning'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-exclamation-triangle-fill text-amber-600 text-base"></i>
                <span class="font-medium">{{ session('warning') }}</span>
            </div>
            <button type="button" class="text-amber-600 hover:text-amber-900 transition" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <!-- Barra de Acciones Superior -->
    <div class="flex items-center justify-between gap-3 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Total de Cuentas: <span class="text-slate-900 font-bold">{{ $catalogos->count() }}</span>
        </div>

        <div class="flex items-center gap-2">
            @if ($catalogos->isEmpty())
                <a href="{{ route('catalogo.importarVista') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition">
                    <i class="bi bi-upload text-sm"></i>
                    Importar Catálogo
                </a>
            @endif

            <button type="button" onclick="document.getElementById('modalAgregar').showModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                <i class="bi bi-plus-lg text-sm"></i>
                Agregar Cuenta
            </button>
        </div>
    </div>

    <!-- Tabla de Cuentas (Sin desbordamiento ni scroll innecesario) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-full">
                <thead>
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3.5 px-6 font-semibold w-2/12">Código</th>
                        <th class="py-3.5 px-6 font-semibold w-7/12">Nombre de la Cuenta</th>
                        <th class="py-3.5 px-6 font-semibold w-3/12 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($catalogos as $cuenta)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-6 font-mono font-bold text-cyan-600 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md bg-cyan-50 border border-cyan-200/60">
                                    {{ $cuenta->codigo_cuenta }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 font-medium text-slate-800">
                                {{ $cuenta->nombre_cuenta }}
                            </td>
                            <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" onclick="document.getElementById('modalEditar{{ $cuenta->id_catalogo }}').showModal()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                                        <i class="bi bi-pencil text-slate-500"></i> Editar
                                    </button>

                                    <form action="{{ route('catalogo.destroy', $cuenta->id_catalogo) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition" onclick="return confirm('¿Está seguro de eliminar esta cuenta contable?')">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Editar (Centrado Nativo) -->
                        <dialog id="modalEditar{{ $cuenta->id_catalogo }}" class="fixed inset-0 m-auto rounded-2xl p-0 backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm max-w-lg w-full border-0 shadow-2xl overflow-hidden">
                            <form method="POST" action="{{ route('catalogo.update', $cuenta->id_catalogo) }}" class="w-full">
                                @csrf @method('PUT')
                                <div class="bg-slate-900 px-6 py-4 border-b border-slate-800 flex items-center justify-between text-white">
                                    <h5 class="font-bold text-base flex items-center gap-2">
                                        <i class="bi bi-pencil-square text-cyan-400"></i>
                                        Editar Cuenta Contable
                                    </h5>
                                    <button type="button" onclick="document.getElementById('modalEditar{{ $cuenta->id_catalogo }}').close()" class="text-slate-400 hover:text-white transition">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="p-6 space-y-4 bg-white">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Código de Cuenta</label>
                                        <input type="text" name="codigo_cuenta" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition" value="{{ $cuenta->codigo_cuenta }}" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nombre de Cuenta</label>
                                        <input type="text" name="nombre_cuenta" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition" value="{{ $cuenta->nombre_cuenta }}" required>
                                    </div>
                                </div>
                                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                                    <button type="button" onclick="document.getElementById('modalEditar{{ $cuenta->id_catalogo }}').close()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm">
                                        <i class="bi bi-x-circle text-sm"></i> Cancelar
                                    </button>
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                                        <i class="bi bi-check-lg text-sm"></i> Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </dialog>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="bi bi-journal-x text-4xl text-slate-300"></i>
                                    <p class="text-sm font-medium text-slate-500">No hay cuentas registradas en este catálogo.</p>
                                    <p class="text-xs text-slate-400">Comienza agregando una cuenta manualmente o importando una plantilla.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Agregar Cuenta (Centrado Nativo) -->
    <dialog id="modalAgregar" class="fixed inset-0 m-auto rounded-2xl p-0 backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm max-w-lg w-full border-0 shadow-2xl overflow-hidden">
        <form method="POST" action="{{ route('catalogo.store') }}" class="w-full">
            @csrf
            <div class="bg-slate-900 px-6 py-4 border-b border-slate-800 flex items-center justify-between text-white">
                <h5 class="font-bold text-base flex items-center gap-2">
                    <i class="bi bi-plus-circle-fill text-cyan-400"></i>
                    Agregar Cuenta Contable
                </h5>
                <button type="button" onclick="document.getElementById('modalAgregar').close()" class="text-slate-400 hover:text-white transition">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 bg-white">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Código de Cuenta</label>
                    <input type="text" name="codigo_cuenta" placeholder="Ej: 1101" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nombre de Cuenta</label>
                    <input type="text" name="nombre_cuenta" placeholder="Ej: Efectivo y Equivalentes" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition" required>
                </div>
            </div>
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalAgregar').close()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm">
                    <i class="bi bi-x-circle text-sm"></i> Cancelar
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                    <i class="bi bi-plus-lg text-sm"></i> Agregar Cuenta
                </button>
            </div>
        </form>
    </dialog>

</div>
@endsection