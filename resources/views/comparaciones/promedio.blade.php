@extends('layouts.app')

@section('title', 'Comparación por Promedios')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4 text-center"> Comparación de Empresas por Promedios Generales</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('comparacion.promedio') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Seleccione el año:</label>
                        <select name="anio" class="form-select" required>
                            <option value="">-- Seleccione --</option>
                            @foreach ($anios as $anio)
                                <option value="{{ $anio }}">{{ $anio }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-bar-chart-line"></i> Comparar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
