@extends('layouts.app')
@section('title', 'Balance General - ' . $anio)

@section('content')
<div class="container py-3">
    <div class="row card card-body shadow-sm bg-light mb-3">
        <nav style="--bs-breadcrumb-divider: '>'; font-size: 0.95rem;" aria-label="breadcrumb">
            <ol class="breadcrumb fw-semibold mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('balance.index') }}" class="text-secondary text-decoration-none">Listado de Balance General</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Balance General - {{ $anio }}</li>
            </ol>
        </nav>
    </div>


    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1>Balance General - {{ $anio }}</h1>
        @if ($balance->isEmpty())
            <a href="{{ route('balance.importarVista', ['anio' => $anio]) }}" class="btn btn-primary">Importar Excel</a>
        @endif
    </div>

    <div class="py-2 table-responsive">
    @php
        // Agrupar por clasificación (primer dígito o primer bloque antes del punto)
        $clasificaciones = $balance->groupBy(function ($item) {
            $codigo = $item->catalogo->codigo_cuenta;
            return preg_match('/^\d+(\.\d+)?$/', $codigo)
                ? explode('.', $codigo)[0]
                : substr($codigo, 0, 1);
        });
    @endphp

    @foreach ($clasificaciones as $codigoClasificacion => $itemsClasificacion)
        @php
            $nombreClasificacion = \App\Models\Catalogo::where('codigo_cuenta', $codigoClasificacion)->value('nombre_cuenta') ?? 'Clasificación ' . $codigoClasificacion;

            // Agrupar por subgrupo (segundo bloque o primeros 2–4 dígitos)
            $subgrupos = $itemsClasificacion->groupBy(function ($item) {
                $codigo = $item->catalogo->codigo_cuenta;
                if (preg_match('/^\d+(\.\d+)?$/', $codigo)) {
                    $partes = explode('.', $codigo);
                    return count($partes) >= 2 ? $partes[0] . '.' . $partes[1] : $partes[0];
                } else {
                    return substr($codigo, 0, 4); // ej. 1101
                }
            });
        @endphp

        <h4 class="mt-4 mb-3">{{ strtoupper($nombreClasificacion) }}</h4>
        @php $totalClasificacion = 0; @endphp

        @foreach ($subgrupos as $codigoSubgrupo => $cuentas)
            @php
                $nombreSubgrupo = \App\Models\Catalogo::where('codigo_cuenta', $codigoSubgrupo)->value('nombre_cuenta') ?? 'Grupo ' . $codigoSubgrupo;
                $totalSubgrupo = $cuentas->sum('saldo');
                $totalClasificacion += $totalSubgrupo;
            @endphp

            <table class="table table-hover table-bordered mb-4">
                <thead class="table-light text-center">
                    <tr>
                        <th colspan="2" class="table-secondary text-start">{{ strtoupper($nombreSubgrupo) }}</th>
                    </tr>
                    <tr>
                        <th style="width: 70%">Cuenta</th>
                        <th style="width: 30%">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cuentas as $cuenta)
                        <tr>
                            <td class="{{ $cuenta->saldo == 0.0 ? 'fw-bold' : '' }}">{{ $cuenta->catalogo->nombre_cuenta }}</td>
                            <td class="text-end">{{ $cuenta->saldo == 0.0 ? '' : '$ ' . number_format($cuenta->saldo, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold table-light">
                        <td class="text-end">Total {{ strtolower($nombreSubgrupo) }}</td>
                        <td class="text-end">$ {{ number_format($totalSubgrupo, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @endforeach

        <div class="text-end fw-bold fs-5 mb-5">
            Total {{ strtolower($nombreClasificacion) }}: $ {{ number_format($totalClasificacion, 2) }}
        </div>
    @endforeach

    </div>
</div>
@endsection
