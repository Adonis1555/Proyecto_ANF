@extends('layouts.app')

@section('title', 'Comparación de Ratios')

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
                <span class="text-slate-600 font-semibold">Comparación Interempresarial</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-arrows-collapse text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Comparación de Ratios Financieros</h1>
                    <p class="text-xs text-slate-500">Evaluación frente a frente de indicadores entre dos empresas o periodos</p>
                </div>
            </div>
        </div>

        <a href="{{ route('ratios.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition self-start sm:self-auto">
            <i class="bi bi-arrow-left text-sm"></i>
            Volver a Ratios
        </a>
    </div>

    <!-- Contenedor para Mensajes y Alertas AJAX -->
    <div id="alertError" class="hidden p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2 shadow-sm">
        <i class="bi bi-exclamation-octagon-fill text-rose-600 text-base shrink-0"></i>
        <span id="alertErrorText"></span>
    </div>

    <div id="alertInfo" class="hidden p-4 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-800 text-xs font-medium flex items-center gap-2 shadow-sm">
        <i class="bi bi-info-circle-fill text-cyan-600 text-base shrink-0"></i>
        <span id="alertInfoText"></span>
    </div>

    <!-- Sección de Selección de Empresas (Grid de 2 Columnas) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Empresa A -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                <div class="h-7 w-7 rounded-lg bg-cyan-500/10 text-cyan-600 font-bold flex items-center justify-center text-xs">
                    A
                </div>
                <h3 class="font-bold text-sm text-slate-900">Empresa A (Base de Cotejo)</h3>
            </div>

            <div class="space-y-3">
                <!-- Sector A -->
                <div>
                    <label for="sectorA" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Sector *
                    </label>
                    <select id="sectorA" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white">
                        <option value="">Seleccione un sector</option>
                    </select>
                </div>

                <!-- Empresa A -->
                <div>
                    <label for="empresaA" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Empresa *
                    </label>
                    <select id="empresaA" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed" disabled>
                        <option value="">Primero seleccione un sector</option>
                    </select>
                </div>

                <!-- Año A -->
                <div>
                    <label for="anioA" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Año Fiscal *
                    </label>
                    <select id="anioA" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 font-mono focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed" disabled>
                        <option value="">Primero seleccione una empresa</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Empresa B -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                <div class="h-7 w-7 rounded-lg bg-slate-900 text-cyan-400 font-bold flex items-center justify-center text-xs">
                    B
                </div>
                <h3 class="font-bold text-sm text-slate-900">Empresa B (A Comparar)</h3>
            </div>

            <div class="space-y-3">
                <!-- Empresa B -->
                <div>
                    <label for="empresaB" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Empresa *
                    </label>
                    <select id="empresaB" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white">
                        <option value="">Seleccione una empresa</option>
                    </select>
                </div>

                <!-- Año B -->
                <div>
                    <label for="anioB" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Año Fiscal *
                    </label>
                    <select id="anioB" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 font-mono focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition bg-white disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed" disabled>
                        <option value="">Primero seleccione una empresa</option>
                    </select>
                </div>
            </div>
        </div>

    </div>

    <!-- Botón de Ejecución -->
    <div class="flex flex-col items-center justify-center space-y-3">
        <button id="btnComparar" class="w-full sm:w-auto px-8 py-3 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 active:bg-cyan-800 disabled:bg-slate-300 disabled:cursor-not-allowed transition shadow-md flex items-center justify-center gap-2" disabled>
            <i class="bi bi-lightning-charge-fill text-sm"></i>
            Comparar Ratios Financieros
        </button>

        <div id="loading" class="hidden items-center gap-2 text-xs font-semibold text-cyan-600">
            <i class="bi bi-arrow-repeat animate-spin text-base"></i>
            <span>Procesando métricas y variaciones interempresariales...</span>
        </div>
    </div>

    <!-- Sección de Resultados (Oculta por defecto) -->
    <div id="resultsSection" class="hidden space-y-6 pt-4">

        <!-- Header de Resultados -->
        <div class="bg-slate-900 text-white p-5 rounded-2xl border border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 shadow-lg">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-file-earmark-diff text-cyan-400 text-lg"></i>
                <h3 class="font-bold text-sm tracking-wide">Resultados del Análisis Comparativo</h3>
            </div>
            <span id="totalRatios" class="px-3 py-1 rounded-md bg-slate-800 border border-slate-700 font-mono text-xs font-semibold text-cyan-300"></span>
        </div>

        <!-- Fichas de Información de Empresa A y B -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Info Empresa A -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-2 shadow-sm">
                <h4 class="font-bold text-xs uppercase tracking-wider text-cyan-600 border-b border-slate-100 pb-2">Empresa A</h4>
                <div class="space-y-1 text-xs text-slate-600">
                    <p><strong class="text-slate-800">Nombre:</strong> <span id="infoNombreA"></span></p>
                    <p><strong class="text-slate-800">NIT:</strong> <span id="infoNitA" class="font-mono"></span></p>
                    <p><strong class="text-slate-800">Sector:</strong> <span id="infoSectorA"></span></p>
                    <p><strong class="text-slate-800">Año:</strong> <span id="infoAnioA" class="font-mono"></span></p>
                </div>
            </div>

            <!-- Info Empresa B -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-2 shadow-sm">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">Empresa B</h4>
                <div class="space-y-1 text-xs text-slate-600">
                    <p><strong class="text-slate-800">Nombre:</strong> <span id="infoNombreB"></span></p>
                    <p><strong class="text-slate-800">NIT:</strong> <span id="infoNitB" class="font-mono"></span></p>
                    <p><strong class="text-slate-800">Sector:</strong> <span id="infoSectorB"></span></p>
                    <p><strong class="text-slate-800">Año:</strong> <span id="infoAnioB" class="font-mono"></span></p>
                </div>
            </div>

        </div>

        <!-- Tabla de Comparación de Ratios -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-full">
                    <thead>
                        <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                            <th class="py-3.5 px-6 font-semibold w-4/12">Ratio Financiero</th>
                            <th class="py-3.5 px-6 font-semibold w-3/12 text-center">Empresa A</th>
                            <th class="py-3.5 px-6 font-semibold w-3/12 text-center">Empresa B</th>
                            <th class="py-3.5 px-6 font-semibold w-2/12 text-center">Diferencia (+/-)</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100 text-sm">
                        <!-- Generado dinámicamente mediante jQuery -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        cargarSectores();
        cargarTodasEmpresas();

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
                mostrarError('Error al cargar la lista de sectores.');
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
                mostrarError('Error al cargar las empresas pertenecientes al sector.');
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
                mostrarError('Error al cargar el listado general de empresas.');
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
                mostrarError('Error al cargar los años disponibles para la empresa.');
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

            $('#loading').removeClass('hidden').addClass('flex');
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
                    const mensaje = xhr.responseJSON?.message || 'Error al procesar la comparación interempresarial.';
                    mostrarError(mensaje);
                },
                complete: function() {
                    $('#loading').addClass('hidden').removeClass('flex');
                    $('#btnComparar').prop('disabled', false);
                }
            });
        }

        function mostrarResultados(data) {
            $('#infoNombreA').text(data.empresa_a.nombre);
            $('#infoNitA').text(data.empresa_a.nit);
            $('#infoSectorA').text(data.empresa_a.sector);
            $('#infoAnioA').text(data.empresa_a.anio);

            $('#infoNombreB').text(data.empresa_b.nombre);
            $('#infoNitB').text(data.empresa_b.nit);
            $('#infoSectorB').text(data.empresa_b.sector);
            $('#infoAnioB').text(data.empresa_b.anio);

            $('#totalRatios').text(`${data.total_ratios} ratios evaluados`);

            let tbody = '';
            data.comparacion.forEach(ratio => {
                const valorA = ratio.valor_empresa_a !== null ? `<span class="font-mono font-bold text-slate-800">${ratio.valor_empresa_a}</span>` : '<span class="text-slate-400 italic text-xs">Sin datos</span>';
                const valorB = ratio.valor_empresa_b !== null ? `<span class="font-mono font-bold text-slate-800">${ratio.valor_empresa_b}</span>` : '<span class="text-slate-400 italic text-xs">Sin datos</span>';
                
                let diferencia = '<span class="text-slate-400 italic text-xs">N/A</span>';
                if (ratio.diferencia !== null) {
                    const signo = ratio.diferencia >= 0 ? '+' : '';
                    const colorClase = ratio.diferencia >= 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60' : 'bg-rose-50 text-rose-700 border-rose-200/60';
                    
                    let porcentajeHTML = '';
                    if (ratio.porcentaje_diferencia !== null) {
                        porcentajeHTML = `<span class="text-[10px] opacity-80 block">${signo}${ratio.porcentaje_diferencia}%</span>`;
                    }

                    diferencia = `
                        <span class="inline-flex flex-col items-center justify-center px-2.5 py-1 rounded-md border font-mono font-bold text-xs ${colorClase}">
                            <span>${signo}${ratio.diferencia}</span>
                            ${porcentajeHTML}
                        </span>
                    `;
                }

                tbody += `
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-6 font-medium text-slate-800">${ratio.nombre_ratio}</td>
                        <td class="py-3.5 px-6 text-center">${valorA}</td>
                        <td class="py-3.5 px-6 text-center">${valorB}</td>
                        <td class="py-3.5 px-6 text-center whitespace-nowrap">${diferencia}</td>
                    </tr>
                `;
            });

            $('#tableBody').html(tbody);
            $('#resultsSection').removeClass('hidden');

            $('html, body').animate({
                scrollTop: $('#resultsSection').offset().top - 20
            }, 500);
        }

        function mostrarError(mensaje) {
            $('#alertErrorText').text(mensaje);
            $('#alertError').removeClass('hidden');
            setTimeout(() => {
                $('#alertError').addClass('hidden');
            }, 5000);
        }

        function ocultarAlertas() {
            $('#alertError').addClass('hidden');
            $('#alertInfo').addClass('hidden');
        }
    });
</script>
@endsection