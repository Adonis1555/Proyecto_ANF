{{-- resources/views/proyeccion-ventas/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Proyección de Ventas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Proyección de Ventas - 12 Meses</h3>
    <a href="{{ route('proyeccion.plantilla') }}" class="btn btn-success">
        <i class="bi bi-download"></i> Descargar Plantilla Excel
    </a>
</div>

{{-- Alertas --}}
<div id="alertContainer"></div>

{{-- Card Principal --}}
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Ingreso de Datos Históricos</h5>
    </div>
    <div class="card-body">
        {{-- Selector de método de entrada --}}
        <div class="mb-4">
            <label class="form-label fw-bold">Método de entrada de datos:</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="metodoEntrada" id="radioManual" value="manual" checked>
                <label class="form-check-label" for="radioManual">
                    Ingreso Manual
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="metodoEntrada" id="radioExcel" value="excel">
                <label class="form-check-label" for="radioExcel">
                    Cargar archivo Excel
                </label>
            </div>
        </div>

        <hr>

        {{-- Formulario Manual --}}
        <div id="formManual">
            <h5 class="mb-3">Ingrese las ventas mensuales:</h5>
            <div class="row">
                @php
                    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                              'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                @endphp
                
                @foreach($meses as $index => $mes)
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ $mes }}</label>
                        <input type="number" step="0.01" class="form-control venta-input" 
                               id="mes{{ $index + 1 }}" placeholder="0.00">
                    </div>
                @endforeach
            </div>
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i>NOTA: Si ingresa solo 11 meses, el sistema generará automáticamente el mes 12 con el promedio.
            </div>
        </div>

        {{-- Formulario Excel --}}
        <div id="formExcel" style="display: none;">
            <h5 class="mb-3">Suba el archivo Excel con los datos:</h5>
            <div class="mb-3">
                <label for="archivoExcel" class="form-label">Seleccione el archivo:</label>
                <input class="form-control" type="file" id="archivoExcel" accept=".xlsx,.xls">
            </div>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> 
                <strong>Importante:</strong> El archivo debe tener el formato de la plantilla descargada.
                <br>Columna A: Mes | Columna B: Ventas
            </div>
        </div>

        {{-- Botón Proyectar --}}
        <div class="d-grid gap-2 mt-4">
            <button class="btn btn-primary btn-lg" id="btnProyectar">
                <i class="bi bi-graph-up-arrow"></i> Generar Proyección
            </button>
        </div>
    </div>
</div>

{{-- Sección de Resultados --}}
<div id="resultadosSection" style="display: none;" class="mt-4">
    {{-- Estadísticas --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Estadísticas de Datos Históricos</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-2">
                    <div class="border rounded p-3">
                        <h6 class="text-muted">Promedio</h6>
                        <h4 id="statPromedio" class="text-primary">-</h4>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="border rounded p-3">
                        <h6 class="text-muted">Máximo</h6>
                        <h4 id="statMaximo" class="text-success">-</h4>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="border rounded p-3">
                        <h6 class="text-muted">Mínimo</h6>
                        <h4 id="statMinimo" class="text-danger">-</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3">
                        <h6 class="text-muted">Desviación Estándar</h6>
                        <h4 id="statDesviacion" class="text-warning">-</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3">
                        <h6 class="text-muted">Tendencia General</h6>
                        <h4 id="statTendencia" class="text-info">-</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tablas de Proyecciones --}}
    <div class="row">
        {{-- Mínimos Cuadrados --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Mínimos Cuadrados</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small" id="formulaMC">Fórmula: -</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Mes</th>
                                    <th class="text-end">Proyección</th>
                                </tr>
                            </thead>
                            <tbody id="tablaMC"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Incremento Porcentual --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Incremento Porcentual</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small" id="infoIP">Incremento promedio: -</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Mes</th>
                                    <th class="text-end">Proyección</th>
                                </tr>
                            </thead>
                            <tbody id="tablaIP"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Incremento Absoluto --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Incremento Absoluto</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small" id="infoIA">Incremento promedio: -</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Mes</th>
                                    <th class="text-end">Proyección</th>
                                </tr>
                            </thead>
                            <tbody id="tablaIA"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico Comparativo --}}
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Gráfico Comparativo de Proyecciones</h5>
        </div>
        <div class="card-body">
            <canvas id="chartProyecciones" style="max-height: 400px;"></canvas>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let chart = null;

    // Cambiar entre formulario manual y excel
    $('input[name="metodoEntrada"]').change(function() {
        if ($(this).val() === 'manual') {
            $('#formManual').show();
            $('#formExcel').hide();
        } else {
            $('#formManual').hide();
            $('#formExcel').show();
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

    // Proyección con datos manuales
    function proyectarManual() {
        const datos = {};
        let conteo = 0;

        // Recoger datos de los inputs
        for (let i = 1; i <= 12; i++) {
            const valor = $('#mes' + i).val();
            if (valor !== '' && valor !== null) {
                datos[i] = parseFloat(valor);
                conteo++;
            }
        }

        // Validar que haya al menos 11 meses
        if (conteo < 11) {
            mostrarAlerta('danger', 'Debe ingresar al menos 11 meses de datos');
            return;
        }

        // Enviar datos al servidor
        enviarDatos({ datos_manuales: datos });
    }

    // Proyección con archivo Excel
    function proyectarExcel() {
        const archivo = $('#archivoExcel')[0].files[0];
        
        if (!archivo) {
            mostrarAlerta('danger', 'Debe seleccionar un archivo Excel');
            return;
        }

        const formData = new FormData();
        formData.append('archivo', archivo);

        // Enviar archivo al servidor
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
                $('#btnProyectar').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Procesando...');
            },
            success: function(response) {
                if (response.success) {
                    mostrarResultados(response.data);
                    mostrarAlerta('success', 'Proyección generada exitosamente');
                }
            },
            error: function(xhr) {
                const mensaje = xhr.responseJSON?.message || 'Error al procesar el archivo';
                mostrarAlerta('danger', mensaje);
            },
            complete: function() {
                $('#btnProyectar').prop('disabled', false).html('<i class="bi bi-graph-up-arrow"></i> Generar Proyección');
            }
        });
    }

    // Enviar datos al servidor
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
                $('#btnProyectar').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Procesando...');
            },
            success: function(response) {
                if (response.success) {
                    mostrarResultados(response.data);
                    mostrarAlerta('success', 'Proyección generada exitosamente');
                }
            },
            error: function(xhr) {
                const mensaje = xhr.responseJSON?.message || 'Error al generar la proyección';
                mostrarAlerta('danger', mensaje);
            },
            complete: function() {
                $('#btnProyectar').prop('disabled', false).html('<i class="bi bi-graph-up-arrow"></i> Generar Proyección');
            }
        });
    }

    // Mostrar resultados
    function mostrarResultados(data) {
        // Estadísticas
        $('#statPromedio').text('$' + formatNumber(data.estadisticas.promedio));
        $('#statMaximo').text('$' + formatNumber(data.estadisticas.maximo));
        $('#statMinimo').text('$' + formatNumber(data.estadisticas.minimo));
        $('#statDesviacion').text('$' + formatNumber(data.estadisticas.desviacion_estandar));
        $('#statTendencia').text(data.estadisticas.tendencia_general);

        // Mínimos Cuadrados
        const mc = data.proyecciones.minimos_cuadrados;
        $('#formulaMC').text('Fórmula: ' + mc.formula);
        llenarTabla('#tablaMC', mc.proyecciones);

        // Incremento Porcentual
        const ip = data.proyecciones.incremento_porcentual;
        $('#infoIP').text('Incremento promedio: ' + ip.incremento_promedio);
        llenarTabla('#tablaIP', ip.proyecciones);

        // Incremento Absoluto
        const ia = data.proyecciones.incremento_absoluto;
        $('#infoIA').text('Incremento promedio: $' + formatNumber(ia.incremento_promedio));
        llenarTabla('#tablaIA', ia.proyecciones);

        // Gráfico
        crearGrafico(data);

        // Mostrar sección de resultados
        $('#resultadosSection').show();
        $('html, body').animate({ scrollTop: $('#resultadosSection').offset().top - 20 }, 500);
    }

    // Llenar tabla de proyecciones
    function llenarTabla(selector, proyecciones) {
        let html = '';
        const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        
        for (let i = 1; i <= 12; i++) {
            html += `<tr>
                <td>${meses[i-1]}</td>
                <td class="text-end fw-bold">$${formatNumber(proyecciones[i])}</td>
            </tr>`;
        }
        
        $(selector).html(html);
    }

    // Crear gráfico
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
                        borderColor: 'rgb(13, 110, 253)',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Incremento Porcentual',
                        data: ip,
                        borderColor: 'rgb(25, 135, 84)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Incremento Absoluto',
                        data: ia,
                        borderColor: 'rgb(255, 193, 7)',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Proyecciones de Ventas - Próximos 12 Meses'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return '$' + formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // Formatear números
    function formatNumber(num) {
        return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    // Mostrar alertas
    function mostrarAlerta(tipo, mensaje) {
        const alerta = `
            <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#alertContainer').html(alerta);
        
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endsection