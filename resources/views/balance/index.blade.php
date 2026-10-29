@extends('layouts.app')
@section('title', 'Listado de Balances')

@section('content')
<div class="row card card-body shadow-sm bg-light mb-3">
    <nav style="--bs-breadcrumb-divider: '>'; font-size: 0.95rem;" aria-label="breadcrumb">
        <ol class="breadcrumb fw-semibold mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item active text-muted" aria-current="page">Listado de Balance General</li>
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

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <h1>Balances General</h1>
    <div>
        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#analisisModal">Ver análisis</button>
        <a href="{{ route('balance.importarVista') }}" class="btn btn-primary">Importar Excel</a>
    </div>
</div>

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
            @forelse ($anios as $index => $anio)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>Balance General</td>
                    <td>{{ $anio }}</td>
                    <td>
                        <a href="{{ route('balance.ver', $anio) }}" class="btn btn-success">Ver</a>
                        <form action="{{ route('balance.eliminar', $anio) }}" 
                                    method="POST" 
                                    class="d-inline"
                                    onsubmit="return confirm('¿Está seguro de eliminar el Balance General del año {{ $anio }}?\nSe eliminarán todos los reportes financieros y ratios asociados.\nEsta acción no se puede deshacer.')">
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
                    <td colspan="4">No hay balances registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($anios->isEmpty())
        <div class="alert alert-info alert-dismissible fade show mt-1 ms-2 me-2">
            <i class="bi bi-info-circle"></i>
            ¡No existe Balance General registrado!
        </div>
    @endif
</div>

<!-- Modal de análisis -->
<div class="modal fade" id="analisisModal" tabindex="-1" aria-labelledby="analisisModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content needs-validation" method="GET" action="{{ route('balance.analisis') }}" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="analisisModalLabel">Ver análisis - Balance General</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="pasado" class="form-label">Desde</label>
                    <input type="number" class="form-control" id="pasado" name="pasado" required min="2017">
                    <div class="invalid-feedback">Es necesario ingresar el año de inicio</div>
                </div>
                <div class="mb-3">
                    <label for="actual" class="form-label">Hasta</label>
                    <input type="number" class="form-control" id="actual" name="actual" required min="2017">
                    <div class="invalid-feedback">Es necesario ingresar el año final</div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Ir</button>
            </div>
        </form>
    </div>
</div>
@endsection
