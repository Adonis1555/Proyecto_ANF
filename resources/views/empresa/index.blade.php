{{-- resources/views/empresa/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Empresas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Listado de Empresas</h3>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar Empresa</button>
</div>

<table class="table table-bordered table-hover">
    <thead class="table-primary">
        <tr>
            <th>ID</th>
            <th>NIT</th>
            <th>Nombre</th>
            <th>Sector</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($empresas as $empresa)
            <tr>
                <td>{{ $empresa->id_empresa }}</td>
                <td>{{ $empresa->nit }}</td>
                <td>{{ $empresa->nombre_empresa }}</td>
                <td>{{ $empresa->sector->nombre_sector }}</td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $empresa->id_empresa }}">Editar</button>
                    <form action="{{ route('empresa.destroy', $empresa->id_empresa) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta empresa?')">Eliminar</button>
                    </form>
                </td>
            </tr>

            <!-- Modal Editar -->
            <div class="modal fade" id="modalEditar{{ $empresa->id_empresa }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('empresa.update', $empresa->id_empresa) }}">
                        @csrf @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Empresa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">NIT</label>
                                    <input type="text" name="nit" class="form-control" value="{{ $empresa->nit }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre_empresa" class="form-control" value="{{ $empresa->nombre_empresa }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sector</label>
                                    <select name="id_sector" class="form-select" required>
                                        @foreach ($sectores as $sector)
                                            <option value="{{ $sector->id_sector }}" {{ $empresa->id_sector == $sector->id_sector ? 'selected' : '' }}>
                                                {{ $sector->nombre_sector }}
                                            </option>
                                        @endforeach
                                    </select>
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
        <form method="POST" action="{{ route('empresa.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NIT</label>
                        <input type="text" name="nit" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre_empresa" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sector</label>
                        <select name="id_sector" class="form-select" required>
                            <option value="" disabled selected>Seleccione un sector</option>
                            @foreach ($sectores as $sector)
                                <option value="{{ $sector->id_sector }}">{{ $sector->nombre_sector }}</option>
                            @endforeach
                        </select>
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
