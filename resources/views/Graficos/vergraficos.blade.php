{{-- resources/views/graficos/vergraficos.blade.php --}}
@extends('layouts.app')

@section('title', 'Gráficos de Ratios')

@section('content')
<div class="container-fluid">
    <!-- Contenedor para mensajes de alerta -->
    <div id="alertContainer"></div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Gráfico de Ratios</h3>
                    
                    {{-- Filtros --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="sector" class="form-label">Seleccionar Sector</label>
                            <select id="sector" class="form-select">
                                <option value="">-- Seleccione --</option>
                                @foreach($sectores as $sector)
                                    <option value="{{ $sector->id_sector }}">{{ $sector->nombre_sector }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="empresa" class="form-label">Seleccionar Empresa</label>
                            <select id="empresa" class="form-select" disabled>
                                <option value="">-- Seleccione primero un sector --</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="ratio" class="form-label">Seleccionar Ratio</label>
                            <select id="ratio" class="form-select" disabled>
                                <option value="">-- Seleccione primero una empresa --</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button id="btnGraficar" class="btn btn-primary w-100" disabled>
                                Graficar
                            </button>
                        </div>
                    </div>

                    {{-- Contenedor del gráfico --}}
                    <div id="graficoContainer" class="mt-4" style="display: none;">
                        <canvas id="chartRatios" width="400" height="200"></canvas>
                    </div>

                    {{-- Mensaje cuando no hay datos --}}
                    <div id="mensajeVacio" class="alert alert-info text-center" style="display: none;">
                        <i class="bi bi-info-circle"></i> Seleccione los filtros y presione "Graficar" para visualizar los datos.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let chartInstance = null;

// Función para mostrar alertas
function mostrarAlerta(mensaje, tipo = 'warning') {
    const alertHTML = `
        <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    $('#alertContainer').html(alertHTML);
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        $('.alert').fadeOut();
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
                    mostrarAlerta('Error al cargar empresas', 'danger');
                }
            });
        } else {
            $('#empresa').html('<option value="">-- Seleccione primero un sector --</option>');
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
            mostrarAlerta('Debe seleccionar una empresa y un ratio', 'warning');
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
                if (response.datos.length > 0) {
                    mostrarGrafico(response);
                } else {
                    $('#graficoContainer').hide();
                    $('#mensajeVacio').html(
                        '<i class="bi bi-exclamation-circle"></i> No hay datos disponibles para esta combinación.'
                    ).show();
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
            mostrarAlerta('Error al cargar ratios', 'danger');
        }
    });
}

function mostrarGrafico(data) {
    $('#mensajeVacio').hide();
    $('#graficoContainer').show();

    // Preparar datos
    const labels = data.datos.map(d => d.anio_ratio);
    const valores = data.datos.map(d => parseFloat(d.valor));

    // Destruir gráfico anterior si existe
    if (chartInstance) {
        chartInstance.destroy();
    }

    // Crear nuevo gráfico
    const ctx = document.getElementById('chartRatios').getContext('2d');
    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: `Valores de ${data.ratio.nombre_ratio}`,
                data: valores,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: `${data.ratio.nombre_ratio} - ${data.empresa.nombre_empresa}`
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'Valor'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Años'
                    }
                }
            }
        }
    });
}
</script>
@endsection