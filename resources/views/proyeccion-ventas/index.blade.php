@extends('layouts.app')

@section('title', 'Proyección de Ventas')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Proyección de Ventas</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-graph-up-arrow text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Proyección de Ventas — 12 Meses</h1>
                    <p class="text-xs text-slate-500">Estimación estadística basada en Mínimos Cuadrados e Incrementos Absolutos/Porcentuales</p>
                </div>
            </div>
        </div>

        <a href="{{ route('proyeccion.plantilla') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm self-start sm:self-auto">
            <i class="bi bi-download text-sm"></i>
            Descargar Plantilla Excel
        </a>
    </div>

    <!-- Contenedor para Alertas Dinámicas -->
    <div id="alertContainer" class="space-y-2"></div>

    <!-- Tarjeta Principal de Entrada de Datos -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
        
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="h-9 w-9 rounded-lg bg-slate-900 text-cyan-400 flex items-center justify-center">
                <i class="bi bi-sliders text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-900">Ingreso de Datos Históricos</h3>
                <p class="text-xs text-slate-400">Selecciona la modalidad para cargar los registros de ventas del ejercicio</p>
            </div>
        </div>

        <!-- Selector de Método de Entrada -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <label class="relative flex items-center gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:border-cyan-500 hover:bg-cyan-50/20 transition group">
                <input type="radio" name="metodoEntrada" id="radioManual" value="manual" checked class="w-4 h-4 text-cyan-600 border-slate-300 focus:ring-cyan-500">
                <div class="space-y-0.5">
                    <span class="block text-xs font-bold text-slate-800 uppercase tracking-wider group-hover:text-cyan-600">Ingreso Manual</span>
                    <span class="block text-xs text-slate-400">Digita los montos mes a mes directamente en la plataforma</span>
                </div>
            </label>

            <label class="relative flex items-center gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:border-cyan-500 hover:bg-cyan-50/20 transition group">
                <input type="radio" name="metodoEntrada" id="radioExcel" value="excel" class="w-4 h-4 text-cyan-600 border-slate-300 focus:ring-cyan-500">
                <div class="space-y-0.5">
                    <span class="block text-xs font-bold text-slate-800 uppercase tracking-wider group-hover:text-cyan-600">Cargar Archivo Excel</span>
                    <span class="block text-xs text-slate-400">Importa los datos mediante una plantilla .xlsx predefinida</span>
                </div>
            </label>
        </div>

        <!-- Formulario Manual -->
        <div id="formManual" class="space-y-4 pt-2">
            <div class="flex items-center justify-between">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700">Ventas Mensuales ($)</h4>
                <span class="text-[11px] text-slate-400">Ingresa valores numéricos válidos</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @php
                    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                              'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                @endphp
                
                @foreach($meses as $index => $mes)
                    <div class="space-y-1">
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase">{{ $mes }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-slate-400 font-mono pointer-events-none">$</span>
                            <input type="number" step="0.01" id="mes{{ $index + 1 }}" placeholder="0.00" 
                                   class="venta-input w-full pl-7 pr-3 py-2 rounded-xl border border-slate-300 text-xs font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-3.5 rounded-xl bg-cyan-50 border border-cyan-200/80 text-cyan-800 text-xs flex items-center gap-2">
                <i class="bi bi-info-circle-fill text-cyan-600 text-sm shrink-0"></i>
                <span><strong>Nota:</strong> Si ingresas solo 11 meses, el sistema estimará automáticamente el mes 12 utilizando el promedio histórico.</span>
            </div>
        </div>

        <!-- Formulario Excel (Oculto por defecto) -->
        <div id="formExcel" class="hidden space-y-4 pt-2">
            <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700">Seleccionar Plantilla de Ventas</h4>
            
            <div class="relative border-2 border-dashed border-slate-300 hover:border-cyan-500 hover:bg-cyan-50/30 rounded-2xl p-6 text-center transition group cursor-pointer">
                <input type="file" id="archivoExcel" accept=".xlsx,.xls" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                <div class="flex flex-col items-center justify-center space-y-2">
                    <div class="h-10 w-10 rounded-xl bg-slate-100 text-slate-500 group-hover:bg-cyan-600 group-hover:text-white flex items-center justify-center transition shadow-sm">
                        <i class="bi bi-file-earmark-excel text-xl"></i>
                    </div>
                    <div class="text-xs">
                        <span class="font-semibold text-cyan-600 hover:underline">Haz clic para buscar</span> o arrastra tu hoja de cálculo aquí
                    </div>
                    <p class="text-[11px] text-slate-400">Formatos soportados: .xlsx, .xls</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs flex items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill text-amber-600 text-sm mt-0.5 shrink-0"></i>
                <div>
                    <strong>Estructura Requerida:</strong> El archivo debe mantener el formato de la plantilla oficial (Columna A: Mes | Columna B: Ventas).
                </div>
            </div>
        </div>

        <!-- Botón de Acción -->
        <div class="pt-2 border-t border-slate-100">
            <button id="btnProyectar" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 active:bg-cyan-800 transition shadow-sm">
                <i class="bi bi-graph-up-arrow text-sm"></i>
                Generar Proyección
            </button>
        </div>

    </div>

    <!-- Sección de Resultados (Oculta por defecto) -->
    <div id="resultadosSection" class="hidden space-y-6">

        <!-- Fichas Estadísticas -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-3 flex items-center gap-2">
                <i class="bi bi-calculator text-cyan-600"></i>
                Estadísticas de Datos Históricos
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-center">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Promedio</span>
                    <span id="statPromedio" class="block text-sm font-mono font-extrabold text-cyan-600">-</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Máximo</span>
                    <span id="statMaximo" class="block text-sm font-mono font-extrabold text-emerald-600">-</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mínimo</span>
                    <span id="statMinimo" class="block text-sm font-mono font-extrabold text-rose-600">-</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Desv. Estándar</span>
                    <span id="statDesviacion" class="block text-sm font-mono font-extrabold text-amber-600">-</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1 col-span-2 md:col-span-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tendencia</span>
                    <span id="statTendencia" class="block text-xs font-bold text-slate-800 uppercase">-</span>
                </div>
            </div>
        </div>

        <!-- Grid de Tablas por Método de Proyección -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Mínimos Cuadrados -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="bg-slate-900 px-5 py-3.5 border-b border-slate-800 text-white flex items-center justify-between">
                        <h4 class="font-bold text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="bi bi-graph-up text-cyan-400"></i>
                            Mínimos Cuadrados
                        </h4>
                    </div>
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                        <p class="text-[11px] font-mono text-slate-500" id="formulaMC">Fórmula: -</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100 text-slate-600 text-[11px] uppercase tracking-wider border-b border-slate-200">
                                    <th class="py-2.5 px-4 font-semibold">Mes</th>
                                    <th class="py-2.5 px-4 font-semibold text-right">Proyección</th>
                                </tr>
                            </thead>
                            <tbody id="tablaMC" class="divide-y divide-slate-100 text-xs"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Incremento Porcentual -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="bg-slate-900 px-5 py-3.5 border-b border-slate-800 text-white flex items-center justify-between">
                        <h4 class="font-bold text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="bi bi-percent text-emerald-400"></i>
                            Incremento Porcentual
                        </h4>
                    </div>
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                        <p class="text-[11px] font-mono text-slate-500" id="infoIP">Incremento promedio: -</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100 text-slate-600 text-[11px] uppercase tracking-wider border-b border-slate-200">
                                    <th class="py-2.5 px-4 font-semibold">Mes</th>
                                    <th class="py-2.5 px-4 font-semibold text-right">Proyección</th>
                                </tr>
                            </thead>
                            <tbody id="tablaIP" class="divide-y divide-slate-100 text-xs"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Incremento Absoluto -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="bg-slate-900 px-5 py-3.5 border-b border-slate-800 text-white flex items-center justify-between">
                        <h4 class="font-bold text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="bi bi-plus-slash-minus text-amber-400"></i>
                            Incremento Absoluto
                        </h4>
                    </div>
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                        <p class="text-[11px] font-mono text-slate-500" id="infoIA">Incremento promedio: -</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100 text-slate-600 text-[11px] uppercase tracking-wider border-b border-slate-200">
                                    <th class="py-2.5 px-4 font-semibold">Mes</th>
                                    <th class="py-2.5 px-4 font-semibold text-right">Proyección</th>
                                </tr>
                            </thead>
                            <tbody id="tablaIA" class="divide-y divide-slate-100 text-xs"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Gráfico Comparativo -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i class="bi bi-bar-chart-line text-cyan-600"></i>
                Gráfico Comparativo de Proyecciones
            </h3>
            <div class="relative w-full min-h-[350px]">
                <canvas id="chartProyecciones"></canvas>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let chart = null;

    // Alternar entre formulario manual y excel
    $('input[name="metodoEntrada"]').change(function() {
        if ($(this).val() === 'manual') {
            $('#formManual').removeClass('hidden');
            $('#formExcel').addClass('hidden');
        } else {
            $('#formManual').addClass('hidden');
            $('#formExcel').removeClass('hidden');
        }
    });

    // Botón proyectar
    $('#btnProyectar').click(function() {
        const metodo = $('input[name="metodoEntrada"]:checked').val();
        
        if (metodo === 'manual') {
            proyectarManual();
        } else {
            proyectarExcel();
        }
    });

    function proyectarManual() {
        const datos = {};
        let conteo = 0;

        for (let i = 1; i <= 12; i++) {
            const valor = $('#mes' + i).val();
            if (valor !== '' && valor !== null) {
                datos[i] = parseFloat(valor);
                conteo++;
            }
        }

        if (conteo < 11) {
            mostrarAlerta('danger', 'Debe ingresar al menos 11 meses de datos históricos.');
            return;
        }

        enviarDatos({ datos_manuales: datos });
    }

    function proyectarExcel() {
        const archivo = $('#archivoExcel')[0].files[0];
        
        if (!archivo) {
            mostrarAlerta('danger', 'Debe seleccionar un archivo Excel en formato válido.');
            return;
        }

        const formData = new FormData();
        formData.append('archivo', archivo);

        $.ajax({
            url: '{{ route("proyeccion.proyectar") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#btnProyectar').prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin"></i> Procesando datos...');
            },
            success: function(response) {
                if (response.success) {
                    mostrarResultados(response.data);
                    mostrarAlerta('success', 'Proyección generada exitosamente.');
                }
            },
            error: function(xhr) {
                const mensaje = xhr.responseJSON?.message || 'Error al procesar el archivo cargado.';
                mostrarAlerta('danger', mensaje);
            },
            complete: function() {
                $('#btnProyectar').prop('disabled', false).html('<i class="bi bi-graph-up-arrow text-sm"></i> Generar Proyección');
            }
        });
    }

    function enviarDatos(datos) {
        $.ajax({
            url: '{{ route("proyeccion.proyectar") }}',
            method: 'POST',
            data: JSON.stringify(datos),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#btnProyectar').prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin"></i> Procesando datos...');
            },
            success: function(response) {
                if (response.success) {
                    mostrarResultados(response.data);
                    mostrarAlerta('success', 'Proyección generada exitosamente.');
                }
            },
            error: function(xhr) {
                const mensaje = xhr.responseJSON?.message || 'Error al calcular la proyección estadística.';
                mostrarAlerta('danger', mensaje);
            },
            complete: function() {
                $('#btnProyectar').prop('disabled', false).html('<i class="bi bi-graph-up-arrow text-sm"></i> Generar Proyección');
            }
        });
    }

    function mostrarResultados(data) {
        $('#statPromedio').text('$' + formatNumber(data.estadisticas.promedio));
        $('#statMaximo').text('$' + formatNumber(data.estadisticas.maximo));
        $('#statMinimo').text('$' + formatNumber(data.estadisticas.minimo));
        $('#statDesviacion').text('$' + formatNumber(data.estadisticas.desviacion_estandar));
        $('#statTendencia').text(data.estadisticas.tendencia_general);

        const mc = data.proyecciones.minimos_cuadrados;
        $('#formulaMC').text('Fórmula: ' + mc.formula);
        llenarTabla('#tablaMC', mc.proyecciones);

        const ip = data.proyecciones.incremento_porcentual;
        $('#infoIP').text('Incremento promedio: ' + ip.incremento_promedio);
        llenarTabla('#tablaIP', ip.proyecciones);

        const ia = data.proyecciones.incremento_absoluto;
        $('#infoIA').text('Incremento promedio: $' + formatNumber(ia.incremento_promedio));
        llenarTabla('#tablaIA', ia.proyecciones);

        crearGrafico(data);

        $('#resultadosSection').removeClass('hidden');
        $('html, body').animate({ scrollTop: $('#resultadosSection').offset().top - 20 }, 500);
    }

    function llenarTabla(selector, proyecciones) {
        let html = '';
        const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        
        for (let i = 1; i <= 12; i++) {
            html += `<tr class="hover:bg-slate-50 transition">
                <td class="py-2 px-4 text-slate-700 font-medium">${meses[i-1]}</td>
                <td class="py-2 px-4 text-right font-mono font-bold text-slate-900">$${formatNumber(proyecciones[i])}</td>
            </tr>`;
        }
        
        $(selector).html(html);
    }

    function crearGrafico(data) {
        const ctx = document.getElementById('chartProyecciones').getContext('2d');
        
        if (chart) {
            chart.destroy();
        }

        const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        
        const mc = Object.values(data.proyecciones.minimos_cuadrados.proyecciones);
        const ip = Object.values(data.proyecciones.incremento_porcentual.proyecciones);
        const ia = Object.values(data.proyecciones.incremento_absoluto.proyecciones);

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [
                    {
                        label: 'Mínimos Cuadrados',
                        data: mc,
                        borderColor: '#0891b2', // cyan-600
                        backgroundColor: 'rgba(8, 145, 178, 0.1)',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Incremento Porcentual',
                        data: ip,
                        borderColor: '#059669', // emerald-600
                        backgroundColor: 'rgba(5, 150, 105, 0.1)',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Incremento Absoluto',
                        data: ia,
                        borderColor: '#d97706', // amber-600
                        backgroundColor: 'rgba(217, 119, 6, 0.1)',
                        borderWidth: 2,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { family: 'ui-sans-serif, system-ui', size: 12 } }
                    },
                    title: {
                        display: true,
                        text: 'Comparativo de Métodos de Proyección (Próximos 12 Meses)',
                        color: '#0f172a',
                        font: { size: 13, weight: 'bold', family: 'ui-sans-serif, system-ui' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'ui-monospace, SFMono-Regular', size: 11 },
                            callback: function(value) {
                                return '$' + formatNumber(value);
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'ui-sans-serif, system-ui', size: 11 } }
                    }
                }
            }
        });
    }

    function formatNumber(num) {
        return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    function mostrarAlerta(tipo, mensaje) {
        const bgColors = {
            danger: 'bg-rose-50 border-rose-200 text-rose-800',
            success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
            warning: 'bg-amber-50 border-amber-200 text-amber-800'
        };
        const iconTypes = {
            danger: 'bi-x-circle-fill text-rose-600',
            success: 'bi-check-circle-fill text-emerald-600',
            warning: 'bi-exclamation-triangle-fill text-amber-600'
        };

        const alerta = `
            <div class="flex items-center justify-between p-4 rounded-xl border text-xs font-medium shadow-sm transition-all duration-300 ${bgColors[tipo] || bgColors.warning}">
                <div class="flex items-center gap-2.5">
                    <i class="bi ${iconTypes[tipo] || iconTypes.warning} text-base"></i>
                    <span>${mensaje}</span>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-700 transition" onclick="this.parentElement.remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        `;
        $('#alertContainer').html(alerta);
        
        setTimeout(() => {
            $('#alertContainer .border').fadeOut(300, function() { $(this).remove(); });
        }, 5000);
    }
});
</script>
@endsection