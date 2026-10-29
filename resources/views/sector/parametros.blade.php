@extends('layouts.app')

@section('title', 'Parámetros del Sector')

@section('content')
<div class="container mt-4">
    <h3>Parámetros aceptables para el sector: <strong>{{ $sector->nombre_sector }}</strong></h3>
    <form method="POST" action="{{ route('sector.parametros.update', $sector->id_sector) }}">
        @csrf

        <table class="table table-bordered mt-3">
            <thead class="table-primary">
                <tr>
                    <th>Ratio</th>
                    <th>Valor aceptable</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ratios as $ratio)
                    <tr>
                        <td>{{ $ratio->nombre_ratio }}</td>
                        <td>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="ratios[{{ $ratio->id_ratio }}]" 
                                class="form-control" 
                                value="{{ $valoresExistentes[$ratio->id_ratio] ?? '' }}"
                                placeholder="Ingrese valor aceptable">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Guardar cambios</button>
            <a href="{{ route('sector.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </form>
</div>
@endsection
