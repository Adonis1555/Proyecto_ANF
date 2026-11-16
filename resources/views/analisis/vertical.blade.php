
@extends('layouts.app')

@section('title', 'Análisis Vertical')

@section('content')

@php
    $esBalance = $tipoReporte === 'balance';

    $tituloReporte = $esBalance
        ? 'Balance General'
        : 'Estado de Resultados';

    $configuracion = $esBalance
        ? [
            'activo' => 'Activos',
            'pasivo' => 'Pasivos',
            'patrimonio' => 'Patrimonio',
        ]
        : [
            'ingresos' => 'Ingresos',
            'costos' => 'Costos de venta',
            'gastos' => 'Gastos',
        ];

    $formato = fn ($valor) =>
        '$ ' . number_format((float) $valor, 2, '.', ',');

    $porcentaje = fn ($valor) =>
        $valor === null
            ? 'N/D'
            : number_format($valor, 2) . ' %';

    $baseIngresos = $totales['ingresos'] ?? 0;

    $porcentajeResultado = function ($valor) use ($baseIngresos) {
        return abs($baseIngresos) > 0.00000001
            ? ($valor / $baseIngresos) * 100
            : null;
    };

    $resumen = $esBalance
        ? [
            ['nombre' => 'Total activos',
             'valor' => $totales['activos']],
            ['nombre' => 'Total pasivos',
             'valor' => $totales['pasivos']],
            ['nombre' => 'Total patrimonio',
             'valor' => $totales['patrimonio']],
            ['nombre' => 'Pasivo + patrimonio',
             'valor' => $totales['pasivo_patrimonio']],
        ]
        : [
            ['nombre' => 'Total ingresos',
             'valor' => $totales['ingresos']],
            ['nombre' => 'Costos de venta',
             'valor' => $totales['costos']],
            ['nombre' => 'Total gastos',
             'valor' => $totales['gastos']],
            ['nombre' => 'Resultado del ejercicio',
             'valor' => $totales['utilidad_neta']],
        ];
@endphp

<div class="space-y-6">

    {{-- ENCABEZADO --}}
    <div class="bg-white rounded-2xl border border-slate-200
                shadow-sm p-6">

        <div class="flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-4">

            <div class="space-y-2">

                <nav class="flex items-center gap-2
                            text-xs text-slate-400">

                    <a href="{{ route('dashboard') }}"
                       class="hover:text-cyan-600">
                        Inicio
                    </a>

                    <i class="bi bi-chevron-right"></i>

                    <a href="{{ route('analisis.index') }}"
                       class="hover:text-cyan-600">
                        Análisis Financiero
                    </a>

                    <i class="bi bi-chevron-right"></i>

                    <span class="text-slate-600">
                        Análisis Vertical
                    </span>
                </nav>

                <div class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl
                                bg-cyan-50 text-cyan-600
                                flex items-center justify-center">

                        <i class="bi bi-bar-chart-line text-xl"></i>
                    </div>

                    <div>
                        <h1 class="text-xl font-bold text-slate-900">
                            Análisis Vertical
                        </h1>

                        <p class="text-sm text-slate-500">
                            {{ $tituloReporte }} · {{ $anio }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 print:hidden">

                <button onclick="window.print()"
                        class="px-4 py-2 rounded-xl
                               bg-white border border-slate-200
                               text-slate-700 text-sm font-medium
                               hover:bg-slate-50 transition">

                    <i class="bi bi-printer mr-1"></i>
                    Imprimir
                </button>

                <a href="{{ route('analisis.index') }}"
                   class="px-4 py-2 rounded-xl
                          bg-slate-100 text-slate-700
                          text-sm font-medium
                          hover:bg-slate-200 transition">

                    <i class="bi bi-arrow-left mr-1"></i>
                    Volver
                </a>
            </div>
        </div>
    </div>


    {{-- RESUMEN FINANCIERO --}}
    <div class="grid grid-cols-1 sm:grid-cols-2
                xl:grid-cols-4 gap-4">

        @foreach ($resumen as $item)

            <div class="bg-white border border-slate-200
                        rounded-2xl p-5 shadow-sm">

                <p class="text-xs font-medium text-slate-500">
                    {{ $item['nombre'] }}
                </p>

                <p class="text-xl font-bold text-slate-900
                          mt-2 font-mono">
                    {{ $formato($item['valor']) }}
                </p>
            </div>

        @endforeach
    </div>


    {{-- COMPROBACIÓN DEL BALANCE --}}
    @if ($esBalance)

        @php
            $diferencia = $totales['diferencia'];
            $balanceCuadrado = abs($diferencia) <= 0.01;
        @endphp

        <div class="bg-white border border-slate-200
                    rounded-xl px-5 py-4">

            <div class="flex flex-col sm:flex-row
                        sm:items-center sm:justify-between gap-3">

                <div>
                    <h3 class="text-sm font-semibold text-slate-800">
                        Comprobación del Balance General
                    </h3>

                    <p class="text-xs text-slate-500 mt-1">
                        Activos = Pasivos + Patrimonio
                    </p>
                </div>

                <div class="text-left sm:text-right">

                    <p class="text-sm font-bold
                        {{ $balanceCuadrado
                            ? 'text-emerald-600'
                            : 'text-amber-600' }}">

                        {{ $balanceCuadrado
                            ? 'Balance cuadrado'
                            : 'Diferencia detectada' }}
                    </p>

                    <p class="text-xs text-slate-500 mt-1">
                        Diferencia:
                        {{ $formato($diferencia) }}
                    </p>
                </div>
            </div>
        </div>

    @endif


    {{-- ADVERTENCIAS --}}
    @if ($advertencias->isNotEmpty())

        <div class="bg-amber-50 border border-amber-200
                    rounded-xl p-5">

            <div class="flex items-start gap-3">

                <i class="bi bi-exclamation-triangle
                          text-amber-600"></i>

                <div class="flex-1">

                    <h3 class="text-sm font-semibold text-amber-900">
                        Observaciones sobre los saldos
                    </h3>

                    <p class="text-xs text-amber-800 mt-1">
                        Se encontraron diferencias entre cuentas
                        acumuladoras y sus cuentas de detalle.
                        Los totales se calcularon con las cuentas
                        de detalle identificadas.
                    </p>

                    <ul class="mt-3 space-y-2 text-xs text-amber-800">

                        @foreach ($advertencias as $advertencia)
                            <li>• {{ $advertencia }}</li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>

    @endif


    {{-- DETALLE POR GRUPOS --}}
    @foreach ($configuracion as $clave => $nombreGrupo)

        @php
            $cuentasGrupo = $grupos[$clave] ?? collect();

            $totalGrupo = $esBalance
                ? ($totales[
                    match ($clave) {
                        'activo' => 'activos',
                        'pasivo' => 'pasivos',
                        'patrimonio' => 'patrimonio'
                    }
                ] ?? 0)
                : ($totales[$clave] ?? 0);

            $baseGrupo = $esBalance
                ? ($clave === 'activo'
                    ? $totales['activos']
                    : $totales['pasivo_patrimonio'])
                : $totales['ingresos'];

            $porcentajeGrupo = abs($baseGrupo) > 0.00000001
                ? ($totalGrupo / $baseGrupo) * 100
                : null;
        @endphp

        <div class="bg-white rounded-2xl
                    border border-slate-200
                    shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-200
                        flex items-center justify-between">

                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        {{ $nombreGrupo }}
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        {{ $cuentasGrupo->count() }} cuentas registradas
                    </p>
                </div>

                <span class="text-xs font-semibold text-cyan-700
                             bg-cyan-50 px-3 py-1 rounded-lg">
                    {{ $porcentaje($porcentajeGrupo) }}
                </span>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm text-left">

                    <thead class="bg-slate-900 text-white
                                  text-xs uppercase tracking-wide">

                        <tr>
                            <th class="px-6 py-3 font-semibold">
                                Código
                            </th>

                            <th class="px-6 py-3 font-semibold">
                                Cuenta contable
                            </th>

                            <th class="px-6 py-3 text-right font-semibold">
                                Saldo
                            </th>

                            <th class="px-6 py-3 text-right font-semibold">
                                Participación
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($cuentasGrupo as $cuenta)

                            <tr class="hover:bg-slate-50 transition
                                {{ !$cuenta->es_detalle
                                    ? 'bg-slate-50/70'
                                    : '' }}">

                                <td class="px-6 py-3 font-mono
                                           text-xs text-cyan-700">
                                    {{ $cuenta->codigo }}
                                </td>

                                <td class="px-6 py-3">

                                    <div class="flex items-center gap-2">

                                        <span class="{{ !$cuenta->es_detalle
                                            ? 'font-bold text-slate-900'
                                            : 'text-slate-700' }}">

                                            {{ $cuenta->nombre }}
                                        </span>

                                        @if (!$cuenta->es_detalle)
                                            <span class="text-[10px]
                                                         text-slate-500
                                                         bg-slate-200
                                                         rounded px-2 py-0.5">
                                                Acumuladora
                                            </span>
                                        @endif

                                    </div>
                                </td>

                                <td class="px-6 py-3 text-right
                                           font-mono text-slate-800">
                                    {{ $formato($cuenta->saldo) }}
                                </td>

                                <td class="px-6 py-3 text-right
                                           font-mono font-semibold
                                           text-cyan-700">

                                    {{ $porcentaje($cuenta->porcentaje) }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4"
                                    class="px-6 py-8 text-center
                                           text-slate-400 text-sm">
                                    No hay cuentas en esta categoría.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                    <tfoot>
                        <tr class="bg-slate-100 border-t
                                   border-slate-200">

                            <td colspan="2"
                                class="px-6 py-4 font-bold text-slate-900">
                                TOTAL {{ mb_strtoupper($nombreGrupo) }}
                            </td>

                            <td class="px-6 py-4 text-right
                                       font-mono font-bold text-slate-900">
                                {{ $formato($totalGrupo) }}
                            </td>

                            <td class="px-6 py-4 text-right
                                       font-mono font-bold text-cyan-700">
                                {{ $porcentaje($porcentajeGrupo) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    @endforeach


    {{-- RESULTADOS FINALES --}}
    @if (!$esBalance)

        <div class="bg-white border border-slate-200
                    rounded-2xl shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-200">

                <h2 class="text-base font-bold text-slate-900">
                    Resumen de resultados
                </h2>

                <p class="text-xs text-slate-500 mt-1">
                    Cálculo automático del resultado del ejercicio
                </p>
            </div>

            <div class="divide-y divide-slate-100">

                @foreach ([
                    ['Resultado bruto',
                     $totales['utilidad_bruta']],
                    ['Resultado del ejercicio',
                     $totales['utilidad_neta']]
                ] as $resultado)

                    <div class="px-6 py-4 flex flex-col
                                sm:flex-row sm:justify-between
                                sm:items-center gap-2">

                        <span class="text-sm font-semibold
                                     text-slate-700">
                            {{ $resultado[0] }}
                        </span>

                        <div class="flex items-center gap-5">

                            <span class="font-mono text-sm
                                         text-slate-900 font-bold">
                                {{ $formato($resultado[1]) }}
                            </span>

                            <span class="font-mono text-sm
                                         text-cyan-700 font-bold">
                                {{ $porcentaje(
                                    $porcentajeResultado($resultado[1])
                                ) }}
                            </span>
                        </div>
                    </div>

                @endforeach
            </div>
        </div>

    @endif


</div>

@endsection
