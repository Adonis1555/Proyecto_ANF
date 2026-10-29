@extends('layouts.app')

@section('title', 'Catálogo de Cuentas')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Catálogo de Cuentas</h3>
        <p class="text-muted mb-0">Empresa: <strong>{{ $empresaNombre }}</strong></p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="bg-light p-3 rounded mb-3 d-flex justify-content-end gap-2">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="bi bi-plus-circle me-1"></i>Agregar Cuenta
        </button>

        @if ($catalogos->isEmpty())
            <a href="{{ route('catalogo.importarVista') }}" class="btn btn-primary">
                <i class="bi bi-upload me-1"></i>Importar Catálogo
            </a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th style="width: 15%">Código</th>
                    <th style="width: 55%">Nombre</th>
                    <th style="width: 30%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($catalogos as $cuenta)
                    <tr>
                        <td>{{ $cuenta->codigo_cuenta }}</td>
                        <td>{{ $cuenta->nombre_cuenta }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $cuenta->id_catalogo }}">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>
                            <form action="{{ route('catalogo.destroy', $cuenta->id_catalogo) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta cuenta?')">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Editar -->
                    <div class="modal fade" id="modalEditar{{ $cuenta->id_catalogo }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('catalogo.update', $cuenta->id_catalogo) }}">
                                @csrf @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-dark">
                                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Cuenta</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Código</label>
                                            <input type="text" name="codigo_cuenta" class="form-control" value="{{ $cuenta->codigo_cuenta }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="nombre_cuenta" class="form-control" value="{{ $cuenta->nombre_cuenta }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-1"></i>Guardar cambios
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Agregar -->
    <div class="modal fade" id="modalAgregar" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('catalogo.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Agregar Cuenta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Código</label>
                            <input type="text" name="codigo_cuenta" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre_cuenta" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Agregar
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
