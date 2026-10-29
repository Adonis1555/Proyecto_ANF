{{-- resources/views/analisisVerHor.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-3">
    <div class="row card card-body shadow-sm bg-light mb-3">
        <nav style="--bs-breadcrumb-divider: '>'; font-size: 0.95rem;" aria-label="breadcrumb">
            <ol class="breadcrumb fw-semibold mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('estado-resultados.index') }}" class="text-secondary text-decoration-none">Lista de Estados de Resultados</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Analisis de ER - {{ $anio_menor }} a {{ $anio_mayor }}</li>
            </ol>
        </nav>
    </div>


    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1>Analisis de Estados de Resultados - {{ $anio_menor }} a {{ $anio_mayor }}</h1>
    </div>
    <div class="py-2 table-responsive">
        <table id="reporte" class="table table-bordered table-striped table-hover">
            <thead class="thead">
                <!-- Fila de Títulos Agrupados -->
                <tr class="text-center table-dark">
                    <th scope="col" rowspan="2" class="align-middle">Cuenta</th>
                    <th scope="col" colspan="2">Saldos</th>
                    <th scope="col" colspan="2">Análisis Horizontal</th>
                    <th scope="col" colspan="2">Análisis Vertical</th>
                </tr>
                <!-- Fila de Años -->
                <tr class="text-center table-dark">
                    <th scope="col">{{ $anio_menor }}</th>
                    <th scope="col">{{ $anio_mayor }}</th>
                    <th scope="col">Var. Absoluta ($)</th>
                    <th scope="col">Var. Relativa (%)</th>
                    <th scope="col">{{ $anio_menor }} (%)</th>
                    <th scope="col">{{ $anio_mayor }} (%)</th>
                </tr>
            </thead>
            <tbody>
                <!-- 
                  Iterar sobre la variable '$resultadosCompletos'
                -->
                @if(isset($resultadosCompletos) && !empty($resultadosCompletos))
                    @foreach($resultadosCompletos as $item)
                        <tr>
                            <!-- Cuenta -->
                            <td>{{ $item['cuenta'] }}</td>
                            
                            <!-- Saldos -->
                            <td class="text-end">{{ '$ ' . number_format($item['saldo_menor'], 2) }}</td>
                            <td class="text-end">{{ '$ ' . number_format($item['saldo_mayor'], 2) }}</td>
                            
                            <!-- Análisis Horizontal -->
                            <td class="text-end">
                                <span class="{{ $item['cambio'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ '$ ' . number_format($item['cambio'], 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $item['porcentaje_cambio'] >= 0 ? 'bg-success' : 'bg-danger' }} text-white shadow-sm" style="width: 80px;">
                                    {{ number_format($item['porcentaje_cambio'], 2) . ' %' }}
                                </span>
                            </td> 
                            
                            <!-- Análisis Vertical -->
                            <td class="text-center">{{ number_format($item['vertical_menor'], 2) . ' %' }}</td>
                            <td class="text-center">{{ number_format($item['vertical_mayor'], 2) . ' %' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="text-center">
                            ¡No existe Estado de Resultados registrado para los años seleccionados!
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>    
    </div>
</div>
@endsection
@push('styles')
<style>
    @media print {
        .btn, .card-body.shadow-sm { display: none; }
        .card { box-shadow: none !important; border: none; }
    }
</style>
@endpush