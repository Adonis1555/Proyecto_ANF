@extends('layouts.app')

@section('title', 'Importar Catálogo')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="text-center mb-4">Importar Catálogo de Cuentas</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('catalogo.importar') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Seleccione un archivo Excel</label>
                        <input type="file" class="form-control" id="fileInput" name="file" accept=".xlsx,.xls" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-upload me-2"></i>Guardar
                        </button>
                        <a class="btn btn-danger" href="{{ route('catalogo.index') }}">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6 mt-4 mt-md-0">
        <div class="alert alert-info">
            <h5><i class="bi bi-info-circle me-2"></i>Formato del Archivo</h5>
            <ul>
                <li>Código de cuenta (columna A)</li>
                <li>Nombre de cuenta (columna B)</li>
            </ul>
            <hr>
            <h6>Preguntas Frecuentes:</h6>
            <ul class="mb-0">
                <li><strong>Formato:</strong> archivo .xlsx con dos columnas</li>
                <li><strong>Encabezados:</strong> pueden tener cualquier título</li>
                <li><strong>Validación:</strong> no se permiten duplicados ni celdas vacías</li>
                <li><strong>Tipo de cuenta:</strong> se asigna automáticamente según el primer dígito</li>
            </ul>
        </div>
    </div>
</div>
@endsection
