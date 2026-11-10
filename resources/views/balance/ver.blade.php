@extends('layouts.app')

@section('title', 'Balance General - ' . $anio)

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <a href="{{ route('balance.index') }}" class="hover:text-cyan-600 transition">Listado de Balance General</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Balance {{ $anio }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-journal-text text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Balance General — Año {{ $anio }}</h1>
                    <p class="text-xs text-slate-500">Desglose detallado de clasificaciones, subgrupos y saldos</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            @if ($balance->isEmpty())
                <a href="{{ route('balance.importarVista', ['anio' => $anio]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                    <i class="bi bi-file-earmark-excel text-sm"></i>
                    Importar Excel
                </a>
            @endif
            <a href="{{ route('balance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                <i class="bi bi-arrow-left text-sm"></i>
                Volver a Balances
            </a>
        </div>
    </div>

    @php
        // Agrupar por clasificación (primer dígito o primer bloque antes del punto)
        $clasificaciones = $balance->groupBy(function ($item) {
            $codigo = $item->catalogo->codigo_cuenta;
            return preg_match('/^\d+(\.\d+)?$/', $codigo)
                ? explode('.', $codigo)[0]
                : substr($codigo, 0, 1);
        });
    @endphp

    @forelse ($clasificaciones as $codigoClasificacion => $itemsClasificacion)
        @php
            $nombreClasificacion = \App\Models\Catalogo::where('codigo_cuenta', $codigoClasificacion)->value('nombre_cuenta') ?? 'Clasificación ' . $codigoClasificacion;

            // Agrupar por subgrupo (segundo bloque o primeros 2–4 dígitos)
            $subgrupos = $itemsClasificacion->groupBy(function ($item) {
                $codigo = $item->catalogo->codigo_cuenta;
                if (preg_match('/^\d+(\.\d+)?$/', $codigo)) {
                    $partes = explode('.', $codigo);
                    return count($partes) >= 2 ? $partes[0] . '.' . $partes[1] : $partes[0];
                } else {
                    return substr($codigo, 0, 4);
                }
            });

            $totalClasificacion = 0;
        @endphp

        <!-- Bloque de Clasificación Principal -->
        <div class="space-y-4 pt-2">
            
            <!-- Título de Clasificación -->
            <div class="flex items-center gap-3 border-b border-slate-300 pb-2">
                <span class="h-8 w-8 rounded-lg bg-slate-900 text-cyan-400 font-mono font-bold text-xs flex items-center justify-center">
                    {{ $codigoClasificacion }}
                </span>
                <h2 class="text-base font-extrabold text-slate-900 tracking-wide uppercase">
                    {{ $nombreClasificacion }}
                </h2>
            </div>

            <!-- Listado de Subgrupos y Tablas -->
            <div class="space-y-4">
                @foreach ($subgrupos as $codigoSubgrupo => $cuentas)
                    @php
                        $nombreSubgrupo = \App\Models\Catalogo::where('codigo_cuenta', $codigoSubgrupo)->value('nombre_cuenta') ?? 'Grupo ' . $codigoSubgrupo;
                        $totalSubgrupo = $cuentas->sum('saldo');
                        $totalClasificacion += $totalSubgrupo;
                    @endphp

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        
                        <!-- Header del Subgrupo -->
                        <div class="bg-slate-900 px-6 py-3 border-b border-slate-800 flex items-center justify-between text-white">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-200 flex items-center gap-2">
                                <i class="bi bi-folder2-open text-cyan-400"></i>
                                {{ $nombreSubgrupo }}
                            </span>
                            <span class="text-xs font-mono text-slate-400">Código: {{ $codigoSubgrupo }}</span>
                        </div>

                        <!-- Tabla de Cuentas del Subgrupo -->
                        <div class="w-full overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-full">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-100">
                                        <th class="py-2.5 px-6 font-semibold w-8/12">Nombre de la Cuenta</th>
                                        <th class="py-2.5 px-6 font-semibold w-4/12 text-right">Saldo ($)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach ($cuentas as $cuenta)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3 px-6 text-slate-800 {{ $cuenta->saldo == 0.0 ? 'font-bold text-slate-900' : 'font-medium' }}">
                                                {{ $cuenta->catalogo->nombre_cuenta }}
                                            </td>
                                            <td class="py-3 px-6 text-right font-mono font-semibold {{ $cuenta->saldo == 0.0 ? 'text-slate-400' : 'text-slate-900' }}">
                                                {{ $cuenta->saldo == 0.0 ? '-' : '$ ' . number_format($cuenta->saldo, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-50/90 font-bold border-t border-slate-200 text-sm">
                                        <td class="py-3 px-6 text-right text-slate-600 font-semibold uppercase text-xs tracking-wider">
                                            Total {{ strtolower($nombreSubgrupo) }}
                                        </td>
                                        <td class="py-3 px-6 text-right font-mono text-cyan-700 font-extrabold">
                                            $ {{ number_format($totalSubgrupo, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Resumen Total de Clasificación -->
            <div class="flex items-center justify-between p-4 rounded-xl bg-slate-900 text-white shadow-md border border-slate-800">
                <span class="text-xs font-extrabold uppercase tracking-widest text-slate-300">
                    Total {{ strtolower($nombreClasificacion) }}
                </span>
                <span class="text-base font-mono font-bold text-cyan-400">
                    $ {{ number_format($totalClasificacion, 2) }}
                </span>
            </div>

        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center text-slate-400 space-y-3">
            <div class="flex flex-col items-center justify-center space-y-2">
                <i class="bi bi-file-earmark-x text-4xl text-slate-300"></i>
                <p class="text-sm font-medium text-slate-500">No hay cuentas asociadas a este Balance General.</p>
                <p class="text-xs text-slate-400">Importa la plantilla correspondiente para cargar la información financiera del periodo.</p>
            </div>
            <a href="{{ route('balance.importarVista', ['anio' => $anio]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-700 transition shadow-sm">
                <i class="bi bi-file-earmark-excel text-sm"></i>
                Importar Excel
            </a>
        </div>
    @endforelse

</div>
@endsection