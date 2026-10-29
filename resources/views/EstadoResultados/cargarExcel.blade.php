@extends('layouts.app')

@section('title', 'Subir Estado de Resultados')

@section('content')
<div class="container py-3">
    {{-- Breadcrumb --}}
    <div class="row card card-body shadow-sm bg-light mb-3">
        <nav style="--bs-breadcrumb-divider: '>'; font-size: 0.95rem;" aria-label="breadcrumb">
            <ol class="breadcrumb fw-semibold mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('balance.index') }}" class="text-secondary text-decoration-none">Inicio</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('estado-resultados.index') }}" class="text-secondary text-decoration-none">
                        Listado de Estados
                    </a>
                </li>
                <li class="breadcrumb-item active text-muted" aria-current="page">
                    Importar Estado de Resultados
                </li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        {{-- Formulario --}}
        <div class="col-md-6">
            <h2 class="text-center mb-4">Importar Estado de Resultados</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="uploadForm" 
                          action="{{ route('estado-resultados.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data">
                        @csrf
                        
                        <fieldset class="mb-3">
                            <legend class="h6">Subida de Estado de Resultados</legend>

                            {{-- Campo de Año --}}
                            <div class="mb-3">
                                <label for="anioInput" class="form-label">Año (mínimo 2017)</label>
                                <input type="number" 
                                       class="form-control @error('anio') is-invalid @enderror" 
                                       id="anioInput" 
                                       name="anio" 
                                       min="2017"
                                       placeholder="Ej. 2024" 
                                       value="{{ old('anio', $anio ?? '') }}"
                                       {{ isset($anio) && $anio >= 2017 ? 'readonly' : '' }}
                                       required>
                                <div class="form-text">Por favor, ingrese el año para el estado de resultados.</div>
                                @error('anio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo de Archivo --}}
                            <div class="mb-3">
                                <label for="fileInput" class="form-label">Seleccione un archivo Excel</label>
                                <input type="file" 
                                       class="form-control @error('file') is-invalid @enderror" 
                                       id="fileInput" 
                                       name="file"
                                       accept=".xlsx, .xls" 
                                       required>
                                <div class="form-text">Seleccione un archivo en formato Excel (.xlsx o .xls).</div>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </fieldset>

                        {{-- Botones de acción --}}
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-primary" 
                                    type="submit" 
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top" 
                                    title="Confirmar subida del estado de resultados">
                                <i class="bi bi-upload me-2"></i>Guardar
                            </button>
                            <a class="btn btn-danger" 
                               href="{{ route('estado-resultados.index') }}" 
                               data-bs-toggle="tooltip"
                               data-bs-placement="top" 
                               title="Cancelar subida del estado de resultados">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Información de formato --}}
        <div class="col-md-6 mt-4 mt-md-0">
            <div class="alert alert-info alert-dismissible fade show">
                <h5 class="alert-heading">
                    <i class="bi bi-info-circle me-2"></i>Formato del Archivo
                </h5>
                <p>Asegúrese de que su archivo Excel cumpla con el siguiente formato:</p>
                <ul>
                    <li>Nombre de cuenta (columna A)</li>
                    <li>Saldo de la cuenta (columna B)</li>
                </ul>
                <img src="{{ asset('image/ERE.png') }}" 
                     class="img-fluid mt-2 rounded shadow-sm"
                     alt="Ejemplo de formato Excel">
                <hr>
                <h6>Preguntas Frecuentes:</h6>
                <ul class="mb-0">
                    <li>
                        <strong>¿Qué formato debe tener mi archivo Excel?</strong> 
                        Debe ser un archivo .xlsx o .xls con dos columnas.
                    </li>
                    <li>
                        <strong>¿Los títulos de las columnas son importantes?</strong> 
                        Sí, pero pueden variar.
                    </li>
                    <li>
                        <strong>¿Qué sucede si una cuenta no existe?</strong> 
                        Las cuentas válidas se crearán automáticamente.
                    </li>
                    <li>
                        <strong>¿Puedo subir el mismo año dos veces?</strong> 
                        No, cada año debe ser único. Si desea actualizarlo, primero elimine el anterior.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection