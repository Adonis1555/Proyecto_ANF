@extends('layouts.app')

@section('title', 'Gráficos de Ratios')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <a href="{{ route('ratios.index') }}" class="hover:text-cyan-600 transition">Ratios Financieros</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Gráficos</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-graph-up text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Gráficos de Ratios Financieros</h1>
                    <p class="text-xs text-slate-500">Visualización de tendencias e indicadores por sector y empresa</p>
                </div>
            </div>
        </div>

        <a href="{{ route('ratios.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition self-start sm:self-auto">
            <i class="bi bi-arrow-left text-sm"></i>
            Volver a Ratios
        </a>
    </div>

    <!-- Contenedor para mensajes de alerta -->
    <div id="alertContainer" class="space-y-2"></div>

    <!-- Tarjeta Principal de Filtros y Gráfico -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
        
        <!-- Filtros de Búsqueda -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-slate-50 p-4 rounded-xl border border-slate-100">
            
            <!-- Selector Sector -->
            <div class="md:col-span-3 space-y-1.5">
                <label for="sector" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Sector
                </label>
                <select id="sector" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white">
                    <option value="">-- Seleccione --</option>
                    @foreach($sectores as $sector)
                        <option value="{{ $sector->id_sector }}">{{ $sector->nombre_sector }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Selector Empresa -->
            <div class="md:col-span-3 space-y-1.5">
                <label for="empresa" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Empresa
                </label>
                <select id="empresa" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed" disabled>
                    <option value="">-- Seleccione sector --</option>
                </select>
            </div>

            <!-- Selector Ratio -->
            <div class="md:col-span-3 space-y-1.5">
                <label for="ratio" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Ratio Financiero
                </label>
                <select id="ratio" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed" disabled>
                    <option value="">-- Seleccione empresa --</option>
                </select>
            </div>

            <!-- Botón Graficar -->
            <div class="md:col-span-3">
                <button id="btnGraficar" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 active:bg-cyan-800 disabled:bg-slate-300 disabled:cursor-not-allowed transition shadow-sm" disabled>
                    <i class="bi bi-bar-chart-fill text-sm"></i>
                    Graficar
                </button>
            </div>

        </div>

        <!-- Canvas del Gráfico -->
        <div id="graficoContainer" class="hidden p-4 rounded-xl border border-slate-100 bg-slate-50/50">
            <div class="relative w-full min-h-[350px]">
                <canvas id="chartRatios"></canvas>
            </div>
        </div>

        <!-- Mensaje Estado Vacío -->
        <div id="mensajeVacio" class="p-8 text-center text-slate-400 border-2 border-dashed border-slate-200 rounded-xl space-y-2">
            <i class="bi bi-graph-up-arrow text-3xl text-slate-300"></i>
            <p class="text-sm font-medium text-slate-600">Seleccione los filtros y presione "Graficar" para visualizar la tendencia.</p>
            <p class="text-xs text-slate-400">Los datos se generarán dinámicamente según la empresa y ratio seleccionados.</p>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let chartInstance = null;

// Función para mostrar alertas adaptadas al diseño
function mostrarAlerta(mensaje, tipo = 'warning') {
    const bgColors = {
        warning: 'bg-amber-50 border-amber-200 text-amber-800',
        danger: 'bg-rose-50 border-rose-200 text-rose-800',
        info: 'bg-cyan-50 border-cyan-200 text-cyan-800'
    };
    const iconTypes = {
        warning: 'bi-exclamation-triangle-fill text-amber-600',
        danger: 'bi-x-circle-fill text-rose-600',
        info: 'bi-info-circle-fill text-cyan-600'
    };

    const alertHTML = `
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
    $('#alertContainer').html(alertHTML);
    
    setTimeout(() => {
        $('#alertContainer .border').fadeOut(300, function() { $(this).remove(); });
    }, 5000);
}

$(document).ready(function() {
    // Cargar ratios al iniciar
    cargarRatios();

    // Cuando se selecciona un sector
    $('#sector').change(function() {
        const sectorId = $(this).val();
        $('#empresa').prop('disabled', true).html('<option value="">Cargando...</option>');
        $('#ratio').prop('disabled', true);
        $('#btnGraficar').prop('disabled', true);

        if (sectorId) {
            $.ajax({
                url: `/graficos/empresas/${sectorId}`,
                method: 'GET',
                success: function(empresas) {
                    let options = '<option value="">-- Seleccione --</option>';
                    empresas.forEach(empresa => {
                        options += `<option value="${empresa.id_empresa}">${empresa.nombre_empresa}</option>`;
                    });
                    $('#empresa').html(options).prop('disabled', false);
                },
                error: function() {
                    mostrarAlerta('Error al cargar las empresas pertenecientes al sector', 'danger');
                }
            });
        } else {
            $('#empresa').html('<option value="">-- Seleccione sector --</option>');
        }
    });

    // Cuando se selecciona una empresa
    $('#empresa').change(function() {
        const empresaId = $(this).val();
        if (empresaId) {
            $('#ratio').prop('disabled', false);
            $('#btnGraficar').prop('disabled', false);
        } else {
            $('#ratio').prop('disabled', true);
            $('#btnGraficar').prop('disabled', true);
        }
    });

    // Botón graficar
    $('#btnGraficar').click(function() {
        const empresaId = $('#empresa').val();
        const ratioId = $('#ratio').val();

        if (!empresaId || !ratioId) {
            mostrarAlerta('Debe seleccionar una empresa y un ratio financiero', 'warning');
            return;
        }

        // Obtener datos para el gráfico
        $.ajax({
            url: '/graficos/datos',
            method: 'POST',
            data: {
                empresa_id: empresaId,
                ratio_id: ratioId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.datos && response.datos.length > 0) {
                    mostrarGrafico(response);
                } else {
                    $('#graficoContainer').addClass('hidden');
                    $('#mensajeVacio').html(`
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="bi bi-exclamation-circle text-3xl text-amber-500"></i>
                            <p class="text-sm font-semibold text-slate-700">Sin datos disponibles</p>
                            <p class="text-xs text-slate-400">No hay registros históricos para la combinación seleccionada.</p>
                        </div>
                    `).removeClass('hidden');
                }
            },
            error: function() {
                mostrarAlerta('Error al cargar los datos del gráfico', 'danger');
            }
        });
    });
});

function cargarRatios() {
    $.ajax({
        url: '/graficos/ratios',
        method: 'GET',
        success: function(ratios) {
            let options = '<option value="">-- Seleccione --</option>';
            ratios.forEach(ratio => {
                options += `<option value="${ratio.id_ratio}">${ratio.nombre_ratio}</option>`;
            });
            $('#ratio').html(options);
        },
        error: function() {
            mostrarAlerta('Error al cargar los ratios financieros', 'danger');
        }
    });
}

function mostrarGrafico(data) {
    $('#mensajeVacio').addClass('hidden');
    $('#graficoContainer').removeClass('hidden');

    const labels = data.datos.map(d => d.anio_ratio);
    const valores = data.datos.map(d => parseFloat(d.valor));

    // Destruir gráfico anterior si existe
    if (chartInstance) {
        chartInstance.destroy();
    }

    const ctx = document.getElementById('chartRatios').getContext('2d');
    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: `${data.ratio.nombre_ratio}`,
                data: valores,
                borderColor: '#0891b2', // cyan-600
                backgroundColor: 'rgba(8, 145, 178, 0.1)',
                borderWidth: 2.5,
                pointBackgroundColor: '#0e7490',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.25
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: `${data.ratio.nombre_ratio} — ${data.empresa.nombre_empresa}`,
                    color: '#0f172a',
                    font: {
                        size: 14,
                        weight: 'bold',
                        family: 'ui-sans-serif, system-ui'
                    },
                    padding: { bottom: 20 }
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: { family: 'ui-sans-serif, system-ui', size: 12 }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { family: 'ui-monospace, SFMono-Regular', size: 11 } },
                    title: {
                        display: true,
                        text: 'Valor Calculado',
                        color: '#64748b',
                        font: { size: 11, weight: '600' }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'ui-monospace, SFMono-Regular', size: 11 } },
                    title: {
                        display: true,
                        text: 'Ejercicio Fiscal (Año)',
                        color: '#64748b',
                        font: { size: 11, weight: '600' }
                    }
                }
            }
        }
    });
}
</script>
@endsection