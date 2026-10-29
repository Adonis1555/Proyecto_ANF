@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Breadcrumb --}}
    <div class="row card card-body shadow-sm bg-light mb-3">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb fw-semibold mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none">Inicio</a>
                </li>
                <li class="breadcrumb-item active">
                    <a href="{{ route('ratios.index') }}" class="text-secondary text-decoration-none">Ratios Financieros</a>
                </li>
                @if(isset($anioSeleccionado))
                <li class="breadcrumb-item active text-muted" aria-current="page">
                    Año {{ $anioSeleccionado }}
                </li>
                @endif
            </ol>
        </nav>
    </div>

    @if(empty($aniosDisponibles) && !isset($ratiosCalculados))
        <h2 class="mb-4">{{ $titulo }}</h2>
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No hay años disponibles con Estado de Resultados y Balance General para calcular ratios.
        </div>
    @elseif(!isset($ratiosCalculados))
        <h2 class="mb-4">{{ $titulo }}</h2>
        <div class="card shadow">
            <div class="card-body">

                <h5 class="card-title mb-3">
                    <i class="fas fa-calendar-alt me-2"></i>Seleccionar año para análisis
                </h5>

                <form action="{{ route('ratios.store') }}" method="POST">
                    @csrf

                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Años disponibles:</label>
                            <select name="anio" class="form-select form-select-sm" required>
                                <option value="" selected disabled>Seleccione un año</option>
                                @foreach($aniosDisponibles as $anio)
                                    <option value="{{ $anio }}">{{ $anio }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success btn-sm ">
                                <i class="fas fa-calculator me-2"></i>Consultar Ratios Financieros
                            </button>
                        </div>
                    </div>

                    @error('anio')
                        <div class="alert alert-danger mt-3">{{ $message }}</div>
                    @enderror
                </form>
            </div>
        </div>
    @else
        <h2 class="mb-4">{{ $titulo }} - Año {{ $anioSeleccionado }}</h2>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 60%;">Ratio Financiero</th>
                                <th class="text-center" style="width: 40%;">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($ratiosCalculados) > 0)
                                {{-- Sección: Ratios de Liquidez --}}
                                @php
                                    $ratiosLiquidez = ['Liquidez Corriente', 'Prueba Ácida', 'Capital de Trabajo'];
                                    $tieneRatiosLiquidez = array_intersect(array_keys($ratiosCalculados), $ratiosLiquidez);
                                @endphp
                                @if(count($tieneRatiosLiquidez) > 0)
                                    <tr>
                                        <td colspan="2">
                                            <strong><i class="fas fa-tint me-2"></i>Ratios de Liquidez</strong>
                                        </td>
                                    </tr>
                                    @foreach($ratiosLiquidez as $nombreRatio)
                                        @if(isset($ratiosCalculados[$nombreRatio]))
                                            <tr>
                                                <td class="ps-4">{{ $nombreRatio }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning text-dark fs-6">{{ $ratiosCalculados[$nombreRatio] }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif

                                {{-- Sección: Ratios de Endeudamiento --}}
                                @php
                                    $ratiosEndeudamiento = ['Grado de Endeudamiento', 'Grado de Propiedad', 'Endeudamiento Patrimonial'];
                                    $tieneRatiosEndeudamiento = array_intersect(array_keys($ratiosCalculados), $ratiosEndeudamiento);
                                @endphp
                                @if(count($tieneRatiosEndeudamiento) > 0)
                                    <tr>
                                        <td colspan="2">
                                            <strong><i class="fas fa-balance-scale me-2"></i>Ratios de Endeudamiento</strong>
                                        </td>
                                    </tr>
                                    @foreach($ratiosEndeudamiento as $nombreRatio)
                                        @if(isset($ratiosCalculados[$nombreRatio]))
                                            <tr>
                                                <td class="ps-4">{{ $nombreRatio }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning text-dark fs-6">{{ $ratiosCalculados[$nombreRatio] }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif

                                {{-- Sección: Ratios de Rentabilidad --}}
                                @php
                                    $ratiosRentabilidad = ['Rentabilidad sobre Ventas'];
                                    $tieneRatiosRentabilidad = array_intersect(array_keys($ratiosCalculados), $ratiosRentabilidad);
                                @endphp
                                @if(count($tieneRatiosRentabilidad) > 0)
                                    <tr>
                                        <td colspan="2">
                                            <strong><i class="fas fa-dollar-sign me-2"></i>Ratios de Rentabilidad</strong>
                                        </td>
                                    </tr>
                                    @foreach($ratiosRentabilidad as $nombreRatio)
                                        @if(isset($ratiosCalculados[$nombreRatio]))
                                            <tr>
                                                <td class="ps-4">{{ $nombreRatio }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning text-dark fs-6">
                                                        {{ $ratiosCalculados[$nombreRatio] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif

                                {{-- Sección: Ratios de Actividad --}}
                                @php
                                    $ratiosActividad = ['Rotación de Inventario', 'Días de Inventario', 'Rotación de Cuentas por Cobrar', 'Periodo Medio de Cobranza', 'Rotación de Activos Totales'];
                                    $tieneRatiosActividad = array_intersect(array_keys($ratiosCalculados), $ratiosActividad);
                                @endphp
                                @if(count($tieneRatiosActividad) > 0)
                                    <tr>
                                        <td colspan="2">
                                            <strong><i class="fas fa-sync me-2"></i>Ratios de Actividad</strong>
                                        </td>
                                    </tr>
                                    @foreach($ratiosActividad as $nombreRatio)
                                        @if(isset($ratiosCalculados[$nombreRatio]))
                                            <tr>
                                                <td class="ps-4">{{ $nombreRatio }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning text-dark fs-6">{{ $ratiosCalculados[$nombreRatio] }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                                <tr>
                                    <td colspan="2" class="text-center">
                                        No se pudieron calcular ratios para el año seleccionado
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <a href="{{ route('ratios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Regresar
                    </a>
                </div>
            
        
    @endif
</div>

@push('styles')
<style>
    @media print {
        .btn { display: none; }
        .card { border: none; box-shadow: none; }
    }
    
    .badge.fs-6 {
        font-size: 1rem !important;
        padding: 0.5rem 1rem;
    }
</style>
@endpush
@endsection