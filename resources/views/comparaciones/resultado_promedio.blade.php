@extends('layouts.app')

@section('title', 'Resultados por Promedio')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3 text-center">
        Resultados de Comparación por Promedios Generales — Año {{ $anio }}
    </h3>

    @if (count($resultados) > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Ratio</th>
                        <th>Promedio General</th>
                        @foreach ($empresas as $empresa)
                            <th>{{ $empresa->nombre_empresa }}</th>
                        @endforeach
                        <th>Empresas que Cumplen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($resultados as $fila)
                        <tr>
                            <td>{{ $fila['nombre_ratio'] }}</td>
                            <td>{{ number_format($fila['promedio'], 2) }}</td>
                            @foreach ($empresas as $empresa)
                                <td>
                                    {{ $fila['valores_empresas'][$empresa->id_empresa] ?? '-' }}
                                </td>
                            @endforeach
                            <td>
                                @if (count($fila['cumplen']) > 0)
                                    <span class="badge bg-success">
                                        {{ implode(', ', $fila['cumplen']) }}
                                    </span>
                                @else
                                    <span class="badge bg-danger">Ninguna</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning text-center">
            No se encontraron datos para el año seleccionado.
        </div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ url('/comparacion/promedio') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>
@endsection
