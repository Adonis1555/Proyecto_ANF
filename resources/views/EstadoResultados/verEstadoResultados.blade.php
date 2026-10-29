{{-- resources/views/verEstadoResultados.blade.php --}}
@extends('layouts.app')

@section('title', 'Estado de Resultados - ' . $anio)

@section('content')
<div class="container py-3">
    <div class="row card card-body shadow-sm bg-light mb-3">
        <nav style="--bs-breadcrumb-divider: '>'; font-size: 0.95rem;" aria-label="breadcrumb">
            <ol class="breadcrumb fw-semibold mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('estado-resultados.index') }}" class="text-secondary text-decoration-none">Lista de Estados de Resultados</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Estado de Resultados - {{ $anio }}</li>
            </ol>
        </nav>
    </div>


    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1>Estado de Resultados - {{ $anio }}</h1>
        @if ($estadoResultados->isEmpty())
            <a href="{{ route('balance.importarVista', ['anio' => $anio]) }}" class="btn btn-primary">Importar Excel</a>
        @endif
    </div>
    <div class="py-2 table-responsive">
        <table id="reporte" class="table table-bordered table-striped">
                <thead class="thead">
                    <tr class="text-center table-dark">
                        <th scope="col">Código</th>
                        <th scope="col">Cuenta</th>
                        <th scope="col">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estadoResultados as $index => $reporteFinanciero)
                    <tr>
                        <td>{{ $reporteFinanciero->catalogo->codigo_cuenta }}</td>
                        <td>{{ $reporteFinanciero->catalogo->nombre_cuenta }}</td>
                        <td class="text-start">
                            {{ $reporteFinanciero->saldo == 0.123456789 ? '' :  '$ ' . number_format($reporteFinanciero->saldo, 2, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">¡No existe Estado de Resultados registrado!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
</div>
@endsection
