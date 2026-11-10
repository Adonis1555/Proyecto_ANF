@extends('layouts.app')

@section('title', 'Importar Balance General')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <a href="{{ route('balance.index') }}" class="hover:text-cyan-600 transition">Listado de Balance General</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Importar Balance General</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-file-earmark-excel-fill text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Importar Balance General</h1>
                    <p class="text-xs text-slate-500">Carga masiva del estado de situación financiera (.xlsx, .xls)</p>
                </div>
            </div>
        </div>

        <a href="{{ route('balance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition self-start sm:self-auto">
            <i class="bi bi-arrow-left text-sm"></i>
            Volver a Balances
        </a>
    </div>

    <!-- Grid Contenedor -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Columna Izquierda: Formulario de Carga -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                
                <form id="uploadForm" action="{{ route('balance.importar') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    
                    <div class="space-y-4">
                        <!-- Campo Año -->
                        <div>
                            <label for="anioInput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Año del Balance
                            </label>
                            <input type="number" id="anioInput" name="anio" min="2000"
                                placeholder="Ej. 2024" value="{{ $anio ?? '' }}"
                                @if(isset($anio) && $anio >= 2000) readonly @endif
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition read-only:bg-slate-100 read-only:text-slate-500 read-only:cursor-not-allowed" required>
                        </div>

                        <!-- Selector Drag & Drop -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Seleccionar Archivo Excel
                            </label>
                            
                            <div class="relative border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:border-cyan-500 hover:bg-cyan-50/30 transition group cursor-pointer" id="drop-zone">
                                <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="fileInput" name="file" accept=".xlsx,.xls" required onchange="handleFileSelect(this)">
                                
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <div class="h-12 w-12 rounded-xl bg-slate-100 text-slate-500 group-hover:bg-cyan-500 group-hover:text-white flex items-center justify-center transition shadow-sm">
                                        <i class="bi bi-cloud-arrow-up text-2xl"></i>
                                    </div>
                                    <div class="text-sm">
                                        <span class="font-semibold text-cyan-600 hover:underline">Haz clic para buscar</span> o arrastra tu archivo aquí
                                    </div>
                                    <p class="text-xs text-slate-400">Archivos permitidos: .xlsx, .xls</p>
                                </div>

                                <!-- Previsualización del archivo seleccionado -->
                                <div id="file-info" class="hidden mt-4 p-3 bg-cyan-50 border border-cyan-200 rounded-xl flex items-center justify-between text-xs text-cyan-800">
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="bi bi-file-earmark-spreadsheet text-base text-cyan-600"></i>
                                        <span id="file-name" class="font-semibold truncate">archivo.xlsx</span>
                                    </div>
                                    <i class="bi bi-check-circle-fill text-cyan-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('balance.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition shadow-sm">
                            <i class="bi bi-x-circle text-sm"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                            <i class="bi bi-upload text-sm"></i>
                            Guardar Balance
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Columna Derecha: Instrucciones y Preguntas Frecuentes -->
        <div class="lg:col-span-5">
            <div class="bg-slate-900 text-white rounded-2xl border border-slate-800 shadow-xl p-6 space-y-5">
                
                <div class="flex items-center gap-2.5 border-b border-slate-800 pb-4">
                    <i class="bi bi-info-circle-fill text-cyan-400 text-lg"></i>
                    <h3 class="font-bold text-sm text-white tracking-wide">Formato del Archivo</h3>
                </div>

                <!-- Estructura requerida -->
                <div class="space-y-2 text-xs text-slate-300">
                    <p class="font-semibold text-slate-100">Estructura requerida en la hoja de cálculo:</p>
                    <ul class="space-y-1.5 pl-2">
                        <li class="flex items-center gap-2">
                            <span class="h-5 w-5 rounded bg-cyan-500/20 text-cyan-400 font-bold flex items-center justify-center text-[10px]">A</span>
                            <span>Nombre de la cuenta</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="h-5 w-5 rounded bg-cyan-500/20 text-cyan-400 font-bold flex items-center justify-center text-[10px]">B</span>
                            <span>Saldo de la cuenta</span>
                        </li>
                    </ul>
                </div>

                <div class="border-t border-slate-800/80 pt-4 space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-cyan-400">Preguntas Frecuentes</h4>
                    
                    <ul class="space-y-2.5 text-xs text-slate-300">
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check2 text-cyan-400 mt-0.5"></i>
                            <span><strong>Formato:</strong> Debe ser un archivo <code>.xlsx</code> o <code>.xls</code> con dos columnas principales.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check2 text-cyan-400 mt-0.5"></i>
                            <span><strong>Títulos de Columnas:</strong> No son obligatorios, pero ayudan al sistema a validar los datos.</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

    </div>

</div>

@section('scripts')
<script>
    function handleFileSelect(input) {
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        
        if (input.files && input.files[0]) {
            fileName.textContent = input.files[0].name;
            fileInfo.classList.remove('hidden');
        } else {
            fileInfo.classList.add('hidden');
        }
    }
</script>
@endsection
@endsection