@extends('layouts.app')

@section('title', $titulo ?? 'Estado de Resultados')

@section('content')
<div class="container py-3">
    {{-- Breadcrumb --}}
    <div class="row card card-body shadow-sm bg-light mb-3">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb fw-semibold mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('balance.index') }}" class="text-secondary text-decoration-none">Inicio</a>
                </li>
                <li class="breadcrumb-item active text-muted" aria-current="page">
                    Listado de Estados de Resultados
                </li>
            </ol>
        </nav>
    </div>
    <!-- MOSTRAR ERRORES -->
     @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>¡Advertencia!</strong> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1>Estados de Resultados</h1>
        <div>
            <a class="btn btn-secondary analisis-btn" data-bs-toggle="modal" data-bs-target="#analisisModal">
                Ver análisis
            </a>
            <a href="{{ route('estado-resultados.create') }}" class="btn btn-primary">
                Importar excel
            </a>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="py-2 table-responsive">
        <table class="table table-hover table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>N°</th>
                    <th>Nombre</th>
                    <th>Año</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($listaAnios as $index => $anio)
                    <tr>
                        <th>{{ $index + 1 }}</th>
                        <td>Estado de Resultados</td>
                        <td>{{ $anio }}</td>
                        <td>
                            <a class="btn btn-success btn-sm" href="{{ route('estado-resultados.show', $anio) }}">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <form action="{{ route('estado-resultados.destroy', $anio) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar el Estado de Resultados del año {{ $anio }}?\nSe eliminarán todos los reportes financieros y ratios asociados.\nEsta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No hay estados de resultados disponibles</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal de análisis --}}
<div class="modal fade" id="analisisModal" tabindex="-1" aria-labelledby="analisisModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="analisisModalLabel">Ver análisis - Estado de Resultados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form class="needs-validation" action="{{ route('estado-resultados.analisis') }}" method="GET" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pasado" class="form-label">Año Base (Anterior)</label>
                        <select name="pasado" id="pasado" class="form-select form-select-sm" required>
                            <option value="" selected disabled>Seleccione un año</option>
                            <!-- Usa $aniosDisponibles -->
                            @foreach($aniosDisponibles as $anio)
                                <option value="{{ $anio }}">{{ $anio }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Debe seleccionar un año base.</div>
                    </div>
                    <div class="mb-3">
                        <label for="actual" class="form-label">Año a Comparar (Actual)</label>
                        <select name="actual" id="actual" class="form-select form-select-sm" required>
                            <option value="" selected disabled>Seleccione un año</option>
                             <!-- Usa $aniosDisponibles -->
                            @foreach($aniosDisponibles as $anio)
                                <option value="{{ $anio }}">{{ $anio }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Debe seleccionar un año a comparar.</div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                         <i class="fas fa-calculator me-2"></i>Ir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    // Script de validación de Bootstrap
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    const pasado = document.getElementById('pasado');
                    const actual = document.getElementById('actual');
                    
                    if (pasado.value && actual.value && pasado.value === actual.value) {
                        event.preventDefault();
                        event.stopPropagation();
                        alert('El año base y el año actual no pueden ser iguales.');
                        actual.setCustomValidity("Los años no pueden ser iguales.");
                    } else if (pasado.value && actual.value && pasado.value > actual.value) {
                         event.preventDefault();
                         event.stopPropagation();
                         alert('El año base (Desde) debe ser menor o igual al año actual (Hasta).');
                         pasado.setCustomValidity("El año base debe ser anterior.");
                    }
                    else {
                        actual.setCustomValidity("");
                        pasado.setCustomValidity("");
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>
@endpush