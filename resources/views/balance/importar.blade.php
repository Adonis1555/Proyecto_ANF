@extends('layouts.app')
@section('title', 'Importar Balance General')

@section('content')
<div class="container my-4">
    <div class="row card card-body shadow-sm bg-light mb-3">
        <nav style="--bs-breadcrumb-divider: '>'; font-size: 0.95rem;" aria-label="breadcrumb">
            <ol class="breadcrumb fw-semibold mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('balance.index') }}" class="text-secondary text-decoration-none">Listado de Balance General</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Importar Balance General</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center py-3">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Importar Balance General</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="uploadForm" action="{{ route('balance.importar') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        <fieldset class="mb-3">
                            <legend class="h6">Subida de Balance General</legend>

                            <div class="mb-3">
                                <label for="anioInput" class="form-label">Año</label>
                                <input type="number" class="form-control" id="anioInput" name="anio" min="2000"
                                    placeholder="Ej. 2024" value="{{ $anio ?? '' }}"
                                    @if(isset($anio) && $anio >= 2000) readonly @endif required>
                                <div class="invalid-feedback">Por favor, ingrese el año para el balance general.</div>
                            </div>

                            <div class="mb-3">
                                <label for="fileInput" class="form-label">Seleccione un archivo Excel</label>
                                <input type="file" class="form-control" id="fileInput" name="file"
                                    accept=".xlsx,.xls" required>
                                <div class="invalid-feedback">Seleccione un archivo en formato Excel (.xlsx o .xls).</div>
                            </div>
                        </fieldset>

                        <div class="d-flex justify-content-between">
                            <button class="btn btn-primary" type="submit" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Confirmar subida del balance">
                                <i class="bi bi-upload me-2"></i>Guardar
                            </button>
                            <a class="btn btn-danger" href="{{ route('balance.index') }}" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Cancelar subida del balance">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 mt-4 mt-md-0">
            <div class="alert alert-info alert-dismissible fade show">
                <h5 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Formato del Archivo</h5>
                <p>Asegúrese de que su archivo Excel cumpla con el siguiente formato:</p>
                <ul>
                    <li>Nombre de cuenta (columna A)</li>
                    <li>Saldo de la cuenta (columna B)</li>
                </ul>
                <hr>
                <h6>Preguntas Frecuentes:</h6>
                <ul class="mb-0">
                    <li><strong>¿Qué formato debe tener mi archivo Excel?</strong> Debe ser un archivo .xlsx con dos columnas.</li>
                    <li><strong>¿Los títulos de las columnas son importantes?</strong> No son obligatorios, pero ayudan a validar.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/tooltip.js') }}"></script>
<script src="{{ asset('js/formValidacion.js') }}"></script>
@endsection
