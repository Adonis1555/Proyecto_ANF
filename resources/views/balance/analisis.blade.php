@extends('layouts.app')
@section('title', 'Análisis Balance General')

@section('content')
<div class="container py-3">
    <div class="row card card-body shadow-sm bg-light mb-3">
        <nav style="--bs-breadcrumb-divider: '>'; font-size: 0.95rem;" aria-label="breadcrumb">
            <ol class="breadcrumb fw-semibold mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('balance.index') }}" class="text-secondary text-decoration-none">Listado de Balance General</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Análisis de Balance - {{ $anio_menor }} a {{ $anio_mayor }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1>Análisis de Balance General - {{ $anio_menor }} a {{ $anio_mayor }}</h1>
    </div>

    <div class="py-2 table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead">
                <tr class="text-center table-dark">
                    <th rowspan="2" class="align-middle">Cuenta</th>
                    <th colspan="2">Saldos</th>
                    <th colspan="2">Análisis Horizontal</th>
                    <th colspan="2">Análisis Vertical</th>
                </tr>
                <tr class="text-center table-dark">
                    <th>{{ $anio_menor }}</th>
                    <th>{{ $anio_mayor }}</th>
                    <th>Var. Absoluta ($)</th>
                    <th>Var. Relativa (%)</th>
                    <th>{{ $anio_menor }} (%)</th>
                    <th>{{ $anio_mayor }} (%)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resultadosCompletos as $item)
                    <tr>
                        <td>{{ $item['cuenta'] }}</td>
                        <td class="text-end">{{ '$ ' . number_format($item['saldo_menor'], 2) }}</td>
                        <td class="text-end">{{ '$ ' . number_format($item['saldo_mayor'], 2) }}</td>
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
                        <td class="text-center">{{ number_format($item['vertical_menor'], 2) . ' %' }}</td>
                        <td class="text-center">{{ number_format($item['vertical_mayor'], 2) . ' %' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">¡No existe Balance General registrado para los años seleccionados!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
