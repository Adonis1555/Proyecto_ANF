@extends('layouts.app')

@section('title', 'Resultados por Sector')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3 text-center">
        Resultados de Comparación por Parametros para el Sector de {{ $nombre_sector }} — Año {{ $anio }}
    </h3>

    @if (count($resultados) > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Ratio</th>
                        <th>Valor Aceptable</th>
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
                            <td>{{ $fila['valor_aceptable'] ?? '-' }}</td>
                            @foreach ($empresas as $empresa)
                                <td>
                                    {{ $fila['valores_empresas'][$empresa->nombre_empresa] ?? '-' }}
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
            No se encontraron datos para el sector seleccionado.
        </div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ url('/comparacion/sector') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>
@endsection
