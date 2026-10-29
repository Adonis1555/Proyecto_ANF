@extends('layouts.app')
@section('title', 'Listado de Balances')

@section('content')

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Comparación de Ratios - Economy Manager</title>
    <link rel="stylesheet" href="{{ asset('css/estilo-comparacion-ratios.css') }}">

<style>
    
        .header {
            color: black;
            padding: 25px 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
        }

        .content {
            padding: 30px;
        }

        .comparison-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .empresa-card {
            background-color: #f8f9fa;
            border: 2px solid rgb(13, 110, 253);
            border-radius: 8px;
            padding: 25px;
        }

        .empresa-card h2 {
            color: rgb(13, 110, 253);
            font-size: 20px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
            background-color: white;
        }

        .form-group select:focus {
            outline: none;
            border-color: rgb(13, 110, 253);
        }

        .form-group select:disabled {
            background-color: #f0f0f0;
            cursor: not-allowed;
        }

        .btn-comparar {
            width: 100%;
            padding: 15px;
            background-color: rgb(13, 110, 253);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
        }

        .btn-comparar:hover {
            background-color: rgb(10, 88, 202);
        }

        .btn-comparar:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .loading {
            display: none;
            text-align: center;
            color: rgb(13, 110, 253);
            font-size: 16px;
            margin: 20px 0;
        }

        .loading.active {
            display: block;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.active {
            display: block;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .results-section {
            display: none;
            margin-top: 30px;
        }

        .results-section.active {
            display: block;
        }

        .results-header {
            background-color: rgb(13, 110, 253);
            color: white;
            padding: 15px 20px;
            border-radius: 6px 6px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .results-header h3 {
            font-size: 18px;
            font-weight: 600;
        }

        .empresa-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-top: none;
        }

        .info-card {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }

        .info-card h4 {
            color: rgb(13, 110, 253);
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .info-card p {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
        }

        .info-card strong {
            color: #333;
        }

        .table-container {
            overflow-x: auto;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 6px 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        thead {
            background-color: rgb(13, 110, 253);
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        th:last-child {
            text-align: center;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .valor-positivo {
            color: #28a745;
            font-weight: 600;
        }

        .valor-negativo {
            color: #dc3545;
            font-weight: 600;
        }

        .diferencia-cell {
            text-align: center;
            font-weight: 600;
        }

        .sin-datos {
            color: #999;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .comparison-section {
                grid-template-columns: 1fr;
            }

            .empresa-info {
                grid-template-columns: 1fr;
            }
        }
</style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Comparación de Ratios Financieros</h1>
        </div>

        <div class="content">
            <div class="alert alert-error" id="alertError"></div>
            <div class="alert alert-info" id="alertInfo"></div>

            <div class="comparison-section">
                <!-- Empresa A -->
                <div class="empresa-card">
                    <h2>Empresa A</h2>
                    
                    <div class="form-group">
                        <label for="sectorA">Sector *</label>
                        <select id="sectorA">
                            <option value="">Seleccione un sector</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="empresaA">Empresa *</label>
                        <select id="empresaA" disabled>
                            <option value="">Primero seleccione un sector</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="anioA">Año *</label>
                        <select id="anioA" disabled>
                            <option value="">Primero seleccione una empresa</option>
                        </select>
                    </div>
                </div>

                <!-- Empresa B -->
                <div class="empresa-card">
                    <h2>Empresa B</h2>

                    <div class="form-group">
                        <label for="empresaB">Empresa *</label>
                        <select id="empresaB">
                            <option value="">Seleccione una empresa</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="anioB">Año *</label>
                        <select id="anioB" disabled>
                            <option value="">Primero seleccione una empresa</option>
                        </select>
                    </div>
                </div>
            </div>

            <button class="btn-comparar" id="btnComparar" disabled>Comparar Ratios</button>

            <div class="loading" id="loading">
                <p> Cargando comparación...</p>
            </div>

            <!-- Sección de Resultados -->
            <div class="results-section" id="resultsSection">
                <div class="results-header">
                    <h3>Resultados de la Comparación</h3>
                    <span id="totalRatios"></span>
                </div>

                <div class="empresa-info">
                    <div class="info-card">
                        <h4>Empresa A</h4>
                        <p><strong>Nombre:</strong> <span id="infoNombreA"></span></p>
                        <p><strong>NIT:</strong> <span id="infoNitA"></span></p>
                        <p><strong>Sector:</strong> <span id="infoSectorA"></span></p>
                        <p><strong>Año:</strong> <span id="infoAnioA"></span></p>
                    </div>

                    <div class="info-card">
                        <h4>Empresa B</h4>
                        <p><strong>Nombre:</strong> <span id="infoNombreB"></span></p>
                        <p><strong>NIT:</strong> <span id="infoNitB"></span></p>
                        <p><strong>Sector:</strong> <span id="infoSectorB"></span></p>
                        <p><strong>Año:</strong> <span id="infoAnioB"></span></p>
                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Ratio</th>
                                <th>Empresa A</th>
                                <th>Empresa B</th>
                                <th>+/-</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Configurar CSRF token para todas las peticiones AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Cargar sectores al iniciar
            cargarSectores();
            cargarTodasEmpresas();

            // Event listeners
            $('#sectorA').change(function() {
                const idSector = $(this).val();
                if (idSector) {
                    cargarEmpresasPorSector(idSector);
                } else {
                    $('#empresaA').html('<option value="">Primero seleccione un sector</option>').prop('disabled', true);
                    $('#anioA').html('<option value="">Primero seleccione una empresa</option>').prop('disabled', true);
                }
                verificarFormularioCompleto();
            });

            $('#empresaA').change(function() {
                const idEmpresa = $(this).val();
                if (idEmpresa) {
                    cargarAniosDisponibles(idEmpresa, 'A');
                } else {
                    $('#anioA').html('<option value="">Primero seleccione una empresa</option>').prop('disabled', true);
                }
                verificarFormularioCompleto();
            });

            $('#empresaB').change(function() {
                const idEmpresa = $(this).val();
                if (idEmpresa) {
                    cargarAniosDisponibles(idEmpresa, 'B');
                } else {
                    $('#anioB').html('<option value="">Primero seleccione una empresa</option>').prop('disabled', true);
                }
                verificarFormularioCompleto();
            });

            $('#anioA, #anioB').change(function() {
                verificarFormularioCompleto();
            });

            $('#btnComparar').click(function() {
                compararRatios();
            });

            // Funciones
            function cargarSectores() {
                $.get('/comparacion-ratios/obtener-sectores', function(response) {
                    if (response.success) {
                        let options = '<option value="">Seleccione un sector</option>';
                        response.data.forEach(sector => {
                            options += `<option value="${sector.id_sector}">${sector.nombre_sector}</option>`;
                        });
                        $('#sectorA').html(options);
                    }
                }).fail(function() {
                    mostrarError('Error al cargar los sectores');
                });
            }

            function cargarEmpresasPorSector(idSector) {
                $.get('/comparacion-ratios/obtener-empresas-sector', { id_sector: idSector }, function(response) {
                    if (response.success) {
                        let options = '<option value="">Seleccione una empresa</option>';
                        response.data.forEach(empresa => {
                            options += `<option value="${empresa.id_empresa}">${empresa.nombre_empresa} (${empresa.nit})</option>`;
                        });
                        $('#empresaA').html(options).prop('disabled', false);
                        $('#anioA').html('<option value="">Primero seleccione una empresa</option>').prop('disabled', true);
                    }
                }).fail(function() {
                    mostrarError('Error al cargar las empresas del sector');
                });
            }

            function cargarTodasEmpresas() {
                $.get('/comparacion-ratios/obtener-empresas', function(response) {
                    if (response.success) {
                        let options = '<option value="">Seleccione una empresa</option>';
                        response.data.forEach(empresa => {
                            const sector = empresa.sector ? ` - ${empresa.sector.nombre_sector}` : '';
                            options += `<option value="${empresa.id_empresa}">${empresa.nombre_empresa} (${empresa.nit})${sector}</option>`;
                        });
                        $('#empresaB').html(options);
                    }
                }).fail(function() {
                    mostrarError('Error al cargar las empresas');
                });
            }

            function cargarAniosDisponibles(idEmpresa, empresa) {
                $.get('/comparacion-ratios/obtener-anios', { id_empresa: idEmpresa }, function(response) {
                    if (response.success) {
                        let options = '<option value="">Seleccione un año</option>';
                        response.data.forEach(anio => {
                            options += `<option value="${anio}">${anio}</option>`;
                        });
                        $(`#anio${empresa}`).html(options).prop('disabled', false);
                    }
                }).fail(function() {
                    mostrarError('Error al cargar los años disponibles');
                });
            }

            function verificarFormularioCompleto() {
                const sectorA = $('#sectorA').val();
                const empresaA = $('#empresaA').val();
                const anioA = $('#anioA').val();
                const empresaB = $('#empresaB').val();
                const anioB = $('#anioB').val();

                if (sectorA && empresaA && anioA && empresaB && anioB) {
                    $('#btnComparar').prop('disabled', false);
                } else {
                    $('#btnComparar').prop('disabled', true);
                }
            }

            function compararRatios() {
                const datos = {
                    id_empresa_a: $('#empresaA').val(),
                    anio_empresa_a: $('#anioA').val(),
                    id_empresa_b: $('#empresaB').val(),
                    anio_empresa_b: $('#anioB').val()
                };

                $('#loading').addClass('active');
                $('#btnComparar').prop('disabled', true);
                ocultarAlertas();

                $.ajax({
                    url: '/comparacion-ratios/comparar',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(datos),
                    success: function(response) {
                        if (response.success) {
                            mostrarResultados(response.data);
                        }
                    },
                    error: function(xhr) {
                        const mensaje = xhr.responseJSON?.message || 'Error al realizar la comparación';
                        mostrarError(mensaje);
                    },
                    complete: function() {
                        $('#loading').removeClass('active');
                        $('#btnComparar').prop('disabled', false);
                    }
                });
            }

            function mostrarResultados(data) {
                // Llenar información de empresas
                $('#infoNombreA').text(data.empresa_a.nombre);
                $('#infoNitA').text(data.empresa_a.nit);
                $('#infoSectorA').text(data.empresa_a.sector);
                $('#infoAnioA').text(data.empresa_a.anio);

                $('#infoNombreB').text(data.empresa_b.nombre);
                $('#infoNitB').text(data.empresa_b.nit);
                $('#infoSectorB').text(data.empresa_b.sector);
                $('#infoAnioB').text(data.empresa_b.anio);

                $('#totalRatios').text(`${data.total_ratios} ratios comparados`);

                // Llenar tabla
                let tbody = '';
                data.comparacion.forEach(ratio => {
                    const valorA = ratio.valor_empresa_a !== null ? ratio.valor_empresa_a : '<span class="sin-datos">Sin datos</span>';
                    const valorB = ratio.valor_empresa_b !== null ? ratio.valor_empresa_b : '<span class="sin-datos">Sin datos</span>';
                    
                    let diferencia = '<span class="sin-datos">N/A</span>';
                    if (ratio.diferencia !== null) {
                        const signo = ratio.diferencia >= 0 ? '+' : '';
                        const clase = ratio.diferencia >= 0 ? 'valor-positivo' : 'valor-negativo';
                        diferencia = `<span class="${clase}">${signo}${ratio.diferencia}</span>`;
                        
                        if (ratio.porcentaje_diferencia !== null) {
                            diferencia += `<br><small>(${signo}${ratio.porcentaje_diferencia}%)</small>`;
                        }
                    }

                    tbody += `
                        <tr>
                            <td><strong>${ratio.nombre_ratio}</strong></td>
                            <td>${valorA}</td>
                            <td>${valorB}</td>
                            <td class="diferencia-cell">${diferencia}</td>
                        </tr>
                    `;
                });

                $('#tableBody').html(tbody);
                $('#resultsSection').addClass('active');

                // Scroll suave hacia los resultados
                $('html, body').animate({
                    scrollTop: $('#resultsSection').offset().top - 20
                }, 500);
            }

            function mostrarError(mensaje) {
                $('#alertError').text(mensaje).addClass('active');
                setTimeout(() => {
                    $('#alertError').removeClass('active');
                }, 5000);
            }

            function ocultarAlertas() {
                $('.alert').removeClass('active');
            }
        });
    </script>
</body>
</html>
@endsection