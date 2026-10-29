{{-- resources/views/sector/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Sectores')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Listado de Sectores</h3>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar Sector</button>
</div>

<table class="table table-bordered table-hover">
    <thead class="table-primary">
        <tr>
            <th>ID</th>
            <th>Nombre del Sector</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sectores as $sector)
            <tr>
                <td>{{ $sector->id_sector }}</td>
                <td>{{ $sector->nombre_sector }}</td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $sector->id_sector }}">Editar</button>
                    <form action="{{ route('sector.destroy', $sector->id_sector) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este sector?')">Eliminar</button>
                    </form>
                    <a href="{{ route('sector.parametros', $sector->id_sector) }}" class="btn btn-sm btn-info">Parámetros</a>
                </td>
            </tr>

            <!-- Modal Editar -->
            <div class="modal fade" id="modalEditar{{ $sector->id_sector }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('sector.update', $sector->id_sector) }}">
                        @csrf @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Sector</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Sector</label>
                                    <input type="text" name="nombre_sector" class="form-control" value="{{ $sector->nombre_sector }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </tbody>
</table>

<!-- Modal Agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('sector.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Sector</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Sector</label>
                        <input type="text" name="nombre_sector" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Agregar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
